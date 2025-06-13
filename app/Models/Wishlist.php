<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Added

class Wishlist extends Model
{
    use HasFactory; // Added
    protected $fillable=['user_id','product_id','cart_id','price','amount','quantity'];

    public function product(){
        return $this->belongsTo(Product::class,'product_id')->withCount('variants'); // Added withCount
    }
}
