<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Size extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * The products that belong to the size.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_size');
    }

    /**
     * Get the product variants for the size.
     */
    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
