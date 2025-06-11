<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariant; // Added import

class Cart extends Model
{
    protected $fillable=['user_id','product_id', 'variant_id', 'order_id','quantity','amount','price','status']; // Added 'variant_id'
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function order(){
        return $this->belongsTo(Order::class,'order_id');
    }
}
