<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Color;
use App\Models\Size;
use App\Models\Specification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductVariantTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function product_variant_belongs_to_a_product()
    {
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

        $this->assertInstanceOf(Product::class, $variant->product);
        $this->assertEquals($product->id, $variant->product->id);
    }

    /** @test */
    public function product_variant_can_belong_to_a_color()
    {
        $color = Color::factory()->create();
        $variant = ProductVariant::factory()->create(['color_id' => $color->id]);

        $this->assertInstanceOf(Color::class, $variant->color);
        $this->assertEquals($color->id, $variant->color->id);
    }

    /** @test */
    public function product_variant_can_belong_to_a_size()
    {
        $size = Size::factory()->create(['name' => 'L' . rand(1000,9999)]); // Ensure unique name if factory produces duplicates easily
        $variant = ProductVariant::factory()->create(['size_id' => $size->id]);

        $this->assertInstanceOf(Size::class, $variant->size);
        $this->assertEquals($size->id, $variant->size->id);
    }

    /** @test */
    public function product_variant_can_belong_to_a_specification()
    {
        $specification = Specification::factory()->create();
        $variant = ProductVariant::factory()->create(['specification_id' => $specification->id]);

        $this->assertInstanceOf(Specification::class, $variant->specification);
        $this->assertEquals($specification->id, $variant->specification->id);
    }

    /** @test */
    public function product_variant_can_be_created_without_optional_attributes()
    {
        $variant = ProductVariant::factory()->create([
            'color_id' => null,
            'size_id' => null,
            'specification_id' => null,
        ]);

        $this->assertNull($variant->color);
        $this->assertNull($variant->size);
        $this->assertNull($variant->specification);
        $this->assertInstanceOf(Product::class, $variant->product); // Ensure product is still associated
    }
}
