<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Color;
use App\Models\Size;
use App\Models\Specification;
use App\Models\Category; // Import for factory
use App\Models\Brand;    // Import for factory
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function product_has_many_variants()
    {
        $product = Product::factory()->create();
        ProductVariant::factory()->count(3)->create(['product_id' => $product->id]);

        $this->assertInstanceOf(ProductVariant::class, $product->variants->first());
        $this->assertCount(3, $product->variants);
    }

    /** @test */
    public function product_belongs_to_many_colors()
    {
        $product = Product::factory()->create();
        $colors = Color::factory()->count(2)->create();
        $product->colors()->attach($colors->pluck('id')->toArray());

        $this->assertInstanceOf(Color::class, $product->colors->first());
        $this->assertCount(2, $product->colors);
    }

    /** @test */
    public function product_belongs_to_many_sizes()
    {
        $product = Product::factory()->create();
        // Create unique sizes for testing this specific product
        $size1 = Size::factory()->create(['name' => 'S' . rand(1000,9999)]);
        $size2 = Size::factory()->create(['name' => 'M' . rand(1000,9999)]);

        $product->sizes()->attach([$size1->id, $size2->id]);
        $product->load('sizes'); // Reload the relationship

        $this->assertInstanceOf(Size::class, $product->sizes->first());
        $this->assertCount(2, $product->sizes);
    }

    /** @test */
    public function product_belongs_to_many_specifications()
    {
        $product = Product::factory()->create();
        $specifications = Specification::factory()->count(2)->create();
        $product->specifications()->attach($specifications->pluck('id')->toArray());

        $this->assertInstanceOf(Specification::class, $product->specifications->first());
        $this->assertCount(2, $product->specifications);
    }

    /** @test */
    public function product_belongs_to_category()
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['cat_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $product->cat_info);
        $this->assertEquals($category->id, $product->cat_info->id);
    }

    /** @test */
    public function product_belongs_to_brand()
    {
        $brand = Brand::factory()->create();
        $product = Product::factory()->create(['brand_id' => $brand->id]);
        $this->assertInstanceOf(Brand::class, $product->brand);
        $this->assertEquals($brand->id, $product->brand->id);
    }
}
