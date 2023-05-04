<?php

namespace App\Http\Controllers\seller;

use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function resume()
    {
        $seller_id = Auth::user()->id;

        $order_details = OrderDetail::join('products', 'order_details.product_id', '=', 'products.id')
                        ->where('products.seller_id', $seller_id)
                        ->select('order_details.*')->get();

        if($order_details->isEmpty()) {
            return response()->json([
                "msg" => "Produk Belum ada yang terjual"
            ], 404);
        }

        $detail = [];
        $income = 0;
        $product_sell = 0;
        foreach($order_details as $order_detail) {
            $product_sell += $order_detail->quantity;
            $income += $order_detail->total_price;
            $detail[] = [
                "order_detail_id" => $order_detail->id,
                "product_id" => $order_detail->product_id,
                "product_name" => $order_detail->product->name,
                "product_terjual" => $order_detail->quantity,
                "harga" => $order_detail->price,
                "total_harga" => $order_detail->total_price
            ];
        }

        return response()->json([
            "barang_terjual" => $product_sell,
            "pendapatan" => $income,
            "detail" => $detail
        ]);
    }
}
