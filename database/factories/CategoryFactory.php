<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->unique()->word;
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'summary' => $this->faker->sentence,
            'photo' => $this->faker->imageUrl(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'is_parent' => $this->faker->boolean(80), // 80% chance of being a parent category
            // 'parent_id' will be handled by state or after creating if needed
        ];
    }
}
