<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Cart::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $quantity = $this->faker->numberBetween(1, 5);

        // Default to having a variant, which also has a product
        $variant = ProductVariant::factory()->create();
        $price = $variant->price;

        return [
            'user_id' => User::factory(),
            'product_id' => $variant->product_id, // Set base product_id from variant
            'variant_id' => $variant->id,        // Set variant_id
            'order_id' => null, // Typically null until checkout
            'price' => $price,
            'status' => 'new',
            'quantity' => $quantity,
            'amount' => $price * $quantity,
        ];
    }

    /**
     * Indicate that the cart item is associated with an order.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withOrder()
    {
        return $this->state(function (array $attributes) {
            return [
                'order_id' => Order::factory(), // Assuming OrderFactory exists or will be created if needed for tests
            ];
        });
    }

    /**
     * Indicate that the cart item is for a product without a specific variant (legacy or simple product).
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forSimpleProduct()
    {
        return $this->state(function (array $attributes) {
            $product = Product::factory()->create();
            $price = $product->price; // Using base product price
            $quantity = $this->faker->numberBetween(1, 5);

            return [
                'product_id' => $product->id,
                'variant_id' => null,
                'price' => $price,
                'quantity' => $quantity,
                'amount' => $price * $quantity,
            ];
        });
    }
}
