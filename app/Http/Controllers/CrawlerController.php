<?php

namespace App\Http\Controllers;

use App\Jobs\ProductCrawlJob;
use App\Services\ProductCrawler;

class CrawlerController extends Controller
{
    public function startCrawl()
    {
        $url = 'https://rajasahib.com/collections/new-arrival';
        
        ProductCrawlJob::dispatch($url);
        
        // $crawler = new ProductCrawler();
        // $crawler->crawl($url);
    return redirect('/');
    }
}
