<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id', 'id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
