<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Specification;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SpecificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function specification_belongs_to_many_products()
    {
        $specification = Specification::factory()->create();
        $products = Product::factory()->count(2)->create();

        $specification->products()->attach($products->pluck('id')->toArray());

        $this->assertInstanceOf(Product::class, $specification->products->first());
        $this->assertCount(2, $specification->products);
    }

    /** @test */
    public function specification_has_many_product_variants()
    {
        $specification = Specification::factory()->create();
        ProductVariant::factory()->count(3)->create(['specification_id' => $specification->id]);

        $this->assertInstanceOf(ProductVariant::class, $specification->productVariants->first());
        $this->assertCount(3, $specification->productVariants);
    }
}
