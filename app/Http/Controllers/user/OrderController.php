<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    private function configuration()
    {
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;
    }

    public function order()
    {

        $this->configuration();
        $user_id = Auth::user()->id;
        $carts = Cart::where('user_id', $user_id)->get();

        if(!$carts) {
            return response()->json([
                "msg" => "Cart is Empty"
            ], 404);
        }

        $product_quantity_error = [];
        foreach($carts as $cart) {
            $cart_quantity = $cart->quantity;
            $product_quantity = $cart->product->quantity;

            if($cart_quantity > $product_quantity) {
                $product_quantity_error[] = [
                    "product_id" => $cart->product_id,
                    "product_name" => $cart->product->name,
                    "product_quantity" => $cart->product->quantity,
                    "user_quantity" => $cart->quantity
                ];
            }
        }

        if(count($product_quantity_error) > 0) {
            return response()->json([
                "msg" => "Beberapa Produk kekurangan stok, tolong ubah pada cart",
                "data" => $product_quantity_error
            ]);
        }

        $order_id = "ORD_" . now()->timestamp . '_' . $user_id;

        $order = Order::create([
            "order_id" => $order_id,
            "user_id" => $user_id,
        ]);

        $total_all_ammount = 0;
        foreach($carts as $cart) {
            $total_price = $cart->quantity * $cart->product->price;

            OrderDetail::create([
                "order_id" => $order->id,
                "product_id" => $cart->product_id,
                "quantity" => $cart->quantity,
                "price" => $cart->product->price,
                "total_price" => $total_price
            ]);

            $product = Product::where('id', $cart->product_id)->first();
            $product->quantity = $product->quantity - $cart->quantity;
            $product->save();

            $current_cart = Cart::where('id', $cart->id)->first();
            $current_cart->delete();

            $total_all_ammount += $total_price;
        }

        $params = array(
            'transaction_details' => array(
                'order_id' => $order_id,
                'gross_amount' => $total_all_ammount,
            ),
            'customer_details' => array(
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ),
        );

        $paymentUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;

        $order->total_amount = $total_all_ammount;
        $order->token = $paymentUrl;
        $order->save();

        return response()->json([
            "url" => $paymentUrl
        ]);

    }

    public function confirm_payment()
    {
        $this->configuration();
        $user_id = Auth::user()->id;

        $order = Order::where('user_id', $user_id)->orderByDesc('id')->first();
        if(!$order || $order->status == 'PAID') {
            return response()->json([
                "msg" => "Tidak ada pembelian"
            ], 404);
        }

        try {
            $status = \Midtrans\Transaction::status($order->order_id);
        } catch(Exception $e) {
            return response()->json([
                "msg" => "Pembayaran belum dilakukan"
            ]);
        }

        if($status->transaction_status != 'capture') {
            return response()->json([
                "msg" => "Pembayaran gagal"
            ]);
        }

        $order->status = 'PAID';
        $order->save();

        return response()->json([
            "msg" => "Pembayaran berhasil",
            "data" => OrderDetail::where('order_id', $order->id)->get()
        ]);

    }
}
