<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
use App\Models\Specification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str; // Added

class ProductVariantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductVariant::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'color_id' => null, // Optional: Color::factory()
            'size_id' => null,  // Optional: Size::factory()
            'specification_id' => null, // Optional: Specification::factory()
            'price' => $this->faker->randomFloat(2, 5, 500), // Variant price
            'stock' => $this->faker->numberBetween(0, 100),
            'sku' => 'SKU-' . strtoupper(Str::random(8)), // More robust unique SKU
        ];
    }

    /**
     * Indicate that the variant has a color.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withColor()
    {
        return $this->state(function (array $attributes) {
            return [
                'color_id' => Color::factory(),
            ];
        });
    }

    /**
     * Indicate that the variant has a size.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withSize()
    {
        return $this->state(function (array $attributes) {
            return [
                'size_id' => Size::factory(),
            ];
        });
    }

    /**
     * Indicate that the variant has a specification.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withSpecification()
    {
        return $this->state(function (array $attributes) {
            return [
                'specification_id' => Specification::factory(),
            ];
        });
    }
}
