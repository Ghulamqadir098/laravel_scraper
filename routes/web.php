<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrawlerController;
use App\Http\Controllers\ProductController;

Route::get('/',[ProductController::class,'index']);

Route::get('/start-crawl', [CrawlerController::class, 'startCrawl']);
