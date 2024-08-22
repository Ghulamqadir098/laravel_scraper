<?php

namespace App\Jobs;

use App\Services\ProductCrawler;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProductCrawlJob implements ShouldQueue
{
    use Queueable;
    // public $tries = 3; // Number of retry attempts
    protected $url;
    public $timeout = 60;
    /**
     * Create a new job instance.
     */
    public function __construct(string $url)
    {
        $this->url = $url;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $crawler = new ProductCrawler();
        $crawler->crawl($this->url);
    }
}
