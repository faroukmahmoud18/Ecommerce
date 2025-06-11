<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Size;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SizeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function size_belongs_to_many_products()
    {
        // Ensure unique size names for this test context if factory can produce duplicates
        $size = Size::factory()->create(['name' => 'XL' . rand(1000,9999)]);
        $products = Product::factory()->count(2)->create();

        $size->products()->attach($products->pluck('id')->toArray());
        $size->load('products'); // reload

        $this->assertInstanceOf(Product::class, $size->products->first());
        $this->assertCount(2, $size->products);
    }

    /** @test */
    public function size_has_many_product_variants()
    {
        $size = Size::factory()->create(['name' => 'M' . rand(1000,9999)]);
        ProductVariant::factory()->count(3)->create(['size_id' => $size->id]);

        $this->assertInstanceOf(ProductVariant::class, $size->productVariants->first());
        $this->assertCount(3, $size->productVariants);
    }
}
