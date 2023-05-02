<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::where('seller_id', Auth::user()->id)->get();

        if($products->isEmpty()) {
            return response()->json(['msg' => 'Product Not Found / Empty'], 404);
        }

        return response()->json($products);
    }

    public function show($id)
    {
        $product = Product::where(['seller_id' => Auth::user()->id, 'id' => $id])->first();

        if(!$product) {
            return response()->json(['msg' => 'Product Not Found / Empty'], 404);
        }

        return response()->json($product);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg',
            'price' => 'required|integer',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        $image_name = '';
        if($request->file('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $image_name = str_replace(' ', '_', $request->name) . '_' . now()->timestamp . '.' . $extension;
            $request->file('image')->storeAs('product', $image_name);
        }

        $data = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'seller_id' => $request->user()->id,
            'filename' => $image_name,
            'quantity' => 0
        ]);

        return response()->json([
            "msg" => "Data Berhasil Ditambahkan",
            "data" => $data
        ]);

    }

    public function update(Request $request, $id)
    {

        $product = Product::find($id);

        if(!$product) {
            return response()->json(['msg' => 'Data Not Found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required',
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg',
            'price' => 'required|integer',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        if(Auth::user()->id != $product->seller_id) {
            return response()->json(['msg' => 'Unauthorized'], 401);
        }

        $image_name = $product->filename;
        if($request->file('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $image_name = str_replace(' ', '_', $request->name) . '_' . now()->timestamp . '.' . $extension;
            $request->file('image')->storeAs('product', $image_name);
        }

        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'filename' => $image_name,
        ]);

        return response()->json([
            "msg" => "Data Berhasil Diubah",
            "data" => $product
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if(!$product) {
            return response()->json(['msg' => 'Data Not Found'], 404);
        }

        if(Auth::user()->id != $product->seller_id) {
            return response()->json(['msg' => 'Unauthorized'], 401);
        }

        $product->delete();

        return response()->json([
            "msg" => "Data Berhasil Dihapus",
            "data" => $product
        ]);
    }

    public function update_quantity(Request $request, $id)
    {
        $product = Product::find($id);

        if(!$product) {
            return response()->json(['msg' => 'Data Not Found'], 404);
        }

        if(Auth::user()->id != $product->seller_id) {
            return response()->json(['msg' => 'Unauthorized'], 401);
        }

        $quantity = $product->quantity;
        $new_quantity = $product->quantity + $request->quantity;

        if($new_quantity < 0) {
            return response()->json(['msg' => 'Tidak dapat negatif'], 500);
        }

        $product->quantity = $new_quantity;
        $product->save();

        return response()->json([
            "msg" => "Update quantity berhasil",
            "data" => $product
        ]);
    }
}
