<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specification extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
    ];

    /**
     * The products that belong to the specification.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_specification');
    }

    /**
     * Get the product variants for the specification.
     */
    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
