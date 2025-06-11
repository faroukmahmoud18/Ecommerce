<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->unique()->sentence(3);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'summary' => $this->faker->text,
            'description' => $this->faker->paragraphs(3, true),
            'photo' => $this->faker->imageUrl(), // Placeholder, actual image handling might differ
            'stock' => $this->faker->numberBetween(0, 100), // Base stock, might be unused if all products have variants
            'cat_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'child_cat_id' => null, // Can be set specifically if needed
            'price' => $this->faker->randomFloat(2, 10, 1000), // Base price
            'discount' => $this->faker->optional(0.3, 0)->randomFloat(2, 5, 30), // 30% chance of discount
            'size' => '', // Deprecated, variants will handle sizes
            'condition' => $this->faker->randomElement(['default', 'new', 'hot']),
            'is_featured' => $this->faker->boolean(20),
            'status' => 'active',
        ];
    }
}
