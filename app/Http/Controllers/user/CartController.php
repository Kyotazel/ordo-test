<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartDetailResource;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    public function index()
    {
        $carts = Cart::where('user_id', Auth::user()->id)->get();

        if($carts->isEmpty()) {
            return response()->json([
                "msg" => "Cart is Empty"
            ], 404);
        }

        return CartResource::collection($carts);
    }

    public function show(Request $request)
    {
        $validate = $request->validate([
            "product_id" => "required"
        ]);

        $cart = Cart::where('user_id', Auth::user()->id)->where('product_id', $request->product_id)->first();

        if(!$cart) {
            return response()->json([
                "msg" => "Data Not Found"
            ]);
        }

        return new CartDetailResource($cart);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'product_id' => 'required',
            'quantity' => 'required'
        ]);

        $product = Product::find($request->product_id);
        
        if(!$product) {
            return response()->json([
                'msg' => 'Product Not Found'
            ]);
        } else if($product->quantity < $request->quantity) {
            return response()->json([
                "msg" => 'quantity melebihi batas',
                "tersedia" => $product->quantity
            ]);
        }

        $cart = Cart::where('user_id', Auth::user()->id)->where('product_id', $request->product_id)->first();
        if($cart) {
            return response()->json([
                "msg" => "Product sudah pernah ditambahkan ke cart"
            ], 404);
        }

        $cart = Cart::create([
            "product_id" => $request->product_id,
            "user_id" => Auth::user()->id,
            "quantity" => $request->quantity
        ]);

        return new CartDetailResource($cart);

    }

    public function update(Request $request)
    {
        $validate = $request->validate([
            'product_id' => 'required',
            'quantity' => 'required'
        ]);

        $product = Product::find($request->product_id);

        if(!$product) {
            return response()->json([
                'msg' => 'Product Not Found'
            ]);
        } else if($product->quantity < $request->quantity) {
            return response()->json([
                "msg" => 'quantity melebihi batas',
                "tersedia" => $product->quantity
            ]);
        }

        $cart = Cart::where('user_id', Auth::user()->id)->where('product_id', $request->product_id)->first();
        if(!$cart) {
            return response()->json([
                "msg" => "Produk belum pernah dibeli oleh user ini"
            ], 404);
        }

        $cart->quantity = $request->quantity;
        $cart->save();

        return new CartDetailResource($cart);
    }

    public function destroy(Request $request)
    {
        $validate = $request->validate([
            'product_id' => 'required',
        ]);

        $cart = Cart::where('user_id', Auth::user()->id)->where('product_id', $request->product_id)->first();
        if(!$cart) {
            return response()->json([
                "msg" => "Produk belum pernah dibeli oleh user ini"
            ], 404);
        }

        $cart->delete();

        return response()->json([
            "msg" => "Cart Berhasil Dihapus"
        ]);
    }
}
