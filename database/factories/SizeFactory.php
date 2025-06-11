<?php

namespace Database\Factories;

use App\Models\Size;
use Illuminate\Database\Eloquent\Factories\Factory;

class SizeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Size::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Common sizes, ensuring uniqueness by selecting one and then it won't be picked again by unique() in other contexts if needed
        // For a factory, just picking one is fine.
        return [
            'name' => $this->faker->randomElement(['XS', 'S', 'M', 'L', 'XL', 'XXL']),
        ];
    }
}
