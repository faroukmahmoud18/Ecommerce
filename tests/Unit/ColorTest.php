<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ColorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function color_belongs_to_many_products()
    {
        $color = Color::factory()->create();
        $products = Product::factory()->count(2)->create();

        $color->products()->attach($products->pluck('id')->toArray());

        $this->assertInstanceOf(Product::class, $color->products->first());
        $this->assertCount(2, $color->products);
    }

    /** @test */
    public function color_has_many_product_variants()
    {
        $color = Color::factory()->create();
        ProductVariant::factory()->count(3)->create(['color_id' => $color->id]);

        $this->assertInstanceOf(ProductVariant::class, $color->productVariants->first());
        $this->assertCount(3, $color->productVariants);
    }
}
