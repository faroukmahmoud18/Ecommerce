<?php

namespace Database\Factories;

use App\Models\Specification;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Specification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->word; // e.g., Material, Capacity, Type
        $value = $this->faker->words(2, true); // e.g., Cotton, 128GB, Wireless

        return [
            'name' => $name,
            'value' => $value,
            // Note: uniqueness of name+value combination might be desired, handle in tests or specific states.
        ];
    }
}
