<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'name',
        'image',
        'size',
        'price',
        'quantity',
    ];

    /**
     * Relationship to the Order model.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relationship to the Product model.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
