<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {

        $product = Product::with('images')->get();
        // foreach ($product as $product) {
        //     echo "Product Name: " . $product->name . "\n";
            
        //     foreach ($product->images as $image) {
        //         echo "Image URL: " . $image->image_url . "\n";
        //     }
        // }
        
// dd($product);
        return view('pages.dashboard', compact('product'));
    }
}
