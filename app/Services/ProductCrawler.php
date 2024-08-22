<?php

namespace App\Services;

use Exception;
use App\Models\Product;
use Spatie\Crawler\Crawler;
use Psr\Http\Message\UriInterface;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class ProductCrawler
{
    public function crawl(string $url)
    {
        // set_time_limit(60);
        Crawler::create()
            ->setCrawlObserver(new class extends CrawlObserver {
                protected $baseUrl;
                protected $client;

                public function __construct()
                {
                    $this->client = new \GuzzleHttp\Client();  // Guzzle client for making HTTP requests
                }

                public function willCrawl(UriInterface $url, ?string $linkText): void
                {
                    Log::info("Crawling started for URL: {$url}");
                }
                public function crawled(
                    UriInterface $url,
                    ResponseInterface $response,
                    ?UriInterface $foundOnUrl = null,
                    ?string $linkText = null
                ): void {
                    Log::info("Crawled URL: {$url}");
                    
                    try {

                    
                        $html = $response->getBody();
                      //  Log::info("HTML Content: " . substr($html, 0, 500)); // Log first 500 characters of the response body for inspection
                
                        $domCrawler = new DomCrawler($html);
                
                        // Loop through each product in the gallery
                        $domCrawler->filter('.product-item')->each(function (DomCrawler $node) use ($url) {
                            $name = $node->filter('.product-item__product-title a')->count() ? $node->filter('.product-item__product-title a')->text() : null;
                            $price = $node->filter('.money')->count() ? $node->filter('.money')->text() : null;
                            $productUrl = $node->filter('.product-item__product-title a')->count() ? $node->filter('.product-item__product-title a')->attr('href') : null;
                
                            // Log the extracted values
                            Log::info("Extracted values - Name: {$name}, Price: {$price}, URL: {$productUrl}");
                
                            // Convert relative URL to absolute if necessary
                            if ($productUrl && !filter_var($productUrl, FILTER_VALIDATE_URL)) {
                                $productUrl = $url->getScheme() . '://' . $url->getHost() . $productUrl;
                            }
                
                            // Clean the price string
                            if ($price) {
                                $price = str_replace([',', 'Rs.', ' '], '', $price); // Remove commas, 'Rs.', and spaces
                                $price = floatval($price); // Convert to float
                            }
                                    
                               
                            if ($name && $price && $productUrl) {
                                $description = $this->fetchProductDescription($productUrl);  // Fetch the product description from its detail page
                
                                // Create product with fetched details
                                $product = Product::create([
                                    'name' => $name,
                                    'description' => $description ?? 'No description available',  // Save fetched description
                                    'quantity' => 2,
                                    'price' => $price,
                                ]);
                
                                // Process and save images
                                $node->filter('.image__inner .image__img')->each(function (DomCrawler $imageNode) use ($product) {
                                    $imageUrl = $imageNode->attr('src');
                                    $product->images()->create([
                                        'image_url' => $imageUrl,
                                    ]);
                                });
                
                                Log::info("Product created: {$name}");
                            } else {
                                Log::warning("Failed to extract product details from node.");
                            }
                        });
                
                    } catch (Exception $e) {
                        Log::error("Error occurred: {$e->getMessage()}");
                    }
                }
                
    
                public function crawlFailed(
                    UriInterface $url,
                    RequestException $requestException,
                    ?UriInterface $foundOnUrl = null,
                    ?string $linkText = null
                ): void {
                    Log::error("Crawl failed for URL: {$url} - {$requestException->getMessage()}");
                }
    
                public function finishedCrawling(): void
                {
                    Log::info("Finished crawling");
                }

                // Method to fetch product description from product detail page
                protected function fetchProductDescription(string $productUrl): ?string
                {

                    try {
                        $response = $this->client->request('GET', $productUrl);  // Make a request to the product detail page
                        $domCrawler = new DomCrawler($response->getBody()->getContents());

                        // Extract the description from the product detail page
                        $description = $domCrawler->filter('.product__primary')->count() ? $domCrawler->filter('.product__primary')->text() : null;

                        return $description;

                    } catch (Exception $e) {
                        Log::error("Failed to fetch product description from {$productUrl} - {$e->getMessage()}");
                        return null;
                    }
                }
            })
        //    ->setCrawlProfile(new CrawlInternalUrls($url))  // Use the default crawl profile to handle internal links
        ->setCrawlProfile(new SinglePageCrawlProfile($url)) // Use the custom crawl profile
          
        ->startCrawling($url);
    }
}




/*
namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use App\Models\Product;
use Spatie\Crawler\Crawler;
use Psr\Http\Message\UriInterface;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Spatie\Crawler\CrawlProfiles\CrawlAllUrls;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class ProductCrawler
{
    public function crawl(string $url)
    {

        Crawler::create()
            
            ->setCrawlObserver(new class extends CrawlObserver {
                public function willCrawl(UriInterface $url, ?string $linkText): void
                {
                    Log::info("Crawling started for URL: {$url}");
                }
    
                public function crawled(
                    UriInterface $url,
                    ResponseInterface $response,
                    ?UriInterface $foundOnUrl = null,
                    ?string $linkText = null
                ): void {
                    Log::info("Crawled URL: {$url}");
    
                    try {
                        $domCrawler = new DomCrawler($response->getBody());
    
                        // Loop through each product in the gallery
                        $domCrawler->filter('.product-item')->each(function (DomCrawler $node) {
                            $name = $node->filter('.product-item__product-title')->count() ? $node->filter('.product-item__product-title')->text() : null;
                          
                            $description = $node->filter('.accordion__content')->count() ? $node->filter('.accordion__content')->text() : null;
                           
                            // accordion__content
                            $price = $node->filter('.money')->count() ? $node->filter('.money')->text() : null;
    
                            // Clean the price string
                            if ($price) {
                                $originalPrice = $price; // Log the original price string
                                $price = str_replace([',', 'Rs.', ' '], '', $price); // Remove commas, 'Rs.', and spaces
                                Log::info("Price after str_replace: {$price}"); // Log price after replacing
    
                                $price = floatval($price); // Convert to float
                                Log::info("Original price string: {$originalPrice}, Cleaned price: {$price}"); // Log both original and cleaned price
                            }
    
                            if ($name && $price) {
                                $product = Product::create([
                                    'name' => $name,
                                    'description' => "Product description",
                                    'quantity' => 2,
                                    'price' => $price,
                                ]);
    
                                $node->filter('.image__inner .image__img')->each(function (DomCrawler $imageNode) use ($product) {
                                    $imageUrl = $imageNode->attr('src');
                                    $product->images()->create([
                                        'image_url' => $imageUrl,
                                    ]);
                                });
    
                                Log::info("Product created: {$name}");
                            } else {
                                Log::warning("Failed to extract product details from node.");
                            }
                        });

                    } catch (Exception $e) {
                        dd($e->getMessage());
                    }
                }
    
                public function crawlFailed(
                    UriInterface $url,
                    RequestException $requestException,
                    ?UriInterface $foundOnUrl = null,
                    ?string $linkText = null
                ): void {
                    Log::error("Crawl failed for URL: {$url} - {$requestException->getMessage()}");
                }
    
                public function finishedCrawling(): void
                {
                    Log::info("Finished crawling");
                }
            })
            
            ->setCrawlProfile(new CrawlInternalUrls($url)) // Use the default crawl profile
            // ->setCrawlProfile(new SinglePageCrawlProfile($url)) // Use the custom crawl profile
            ->startCrawling($url);
    }
    
}
*/