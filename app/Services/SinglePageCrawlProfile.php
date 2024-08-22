<?php

namespace App\Services;

use Spatie\Crawler\CrawlProfiles\CrawlProfile;
use Psr\Http\Message\UriInterface;

class SinglePageCrawlProfile extends CrawlProfile
{
    protected $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function shouldCrawl(UriInterface $url): bool
    {
        // Only allow crawling of the initial URL
        return $url->__toString() === $this->baseUrl;
    }
}
