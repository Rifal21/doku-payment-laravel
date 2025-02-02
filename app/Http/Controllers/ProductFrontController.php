<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductFrontController extends Controller
{
    public function index(){
        $products = Product::all();
        return view('product.index' , compact('products'));
    }

    public function show($slug){
        $product = Product::where('slug', $slug)->first();
        return view('product.show', compact('product'));
    }
}
