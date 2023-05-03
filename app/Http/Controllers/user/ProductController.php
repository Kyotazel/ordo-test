<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $product = Product::where('quantity', '>', 0)->get();
        return response()->json($product);
    }

    public function show($id)
    {
        $product = Product::where('quantity', '>', 0)->where('id', $id)->first();
        if(!$product) {
            return response()->json([
                'msg' => 'Product Not Found / Empty'
            ]);
        }

        return response()->json([
            "data" => $product
        ]);
    }
}
