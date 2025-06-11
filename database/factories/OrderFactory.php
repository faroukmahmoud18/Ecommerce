<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Shipping; // Assuming Shipping model exists and is used by Order
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-' . Str::upper(Str::random(10)),
            'sub_total' => $this->faker->randomFloat(2, 50, 500),
            'shipping_id' => null, // Optional: Shipping::factory() if shipping is always present
            'coupon' => $this->faker->optional()->randomFloat(2, 5, 50),
            'total_amount' => $this->faker->randomFloat(2, 60, 600), // Should be sub_total + shipping - coupon
            'quantity' => $this->faker->numberBetween(1, 10),
            'payment_method' => $this->faker->randomElement(['cod', 'paypal', 'card']),
            'payment_status' => $this->faker->randomElement(['paid', 'unpaid']),
            'status' => $this->faker->randomElement(['new', 'process', 'delivered', 'cancel']),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'country' => $this->faker->country,
            'post_code' => $this->faker->optional()->postcode,
            'address1' => $this->faker->streetAddress,
            'address2' => $this->faker->optional()->secondaryAddress,
        ];
    }

     /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            // Recalculate total_amount if sub_total, shipping, or coupon are involved
            $shipping_cost = optional($order->shipping)->price ?? 0;
            $order->total_amount = ($order->sub_total + $shipping_cost) - $order->coupon;
            $order->save();
        });
    }
}
