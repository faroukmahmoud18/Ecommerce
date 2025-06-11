<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Order; // Import if using withOrder state in factory/tests
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cart_can_belong_to_a_product_variant()
    {
        $variant = ProductVariant::factory()->create();
        $cart = Cart::factory()->create(['variant_id' => $variant->id, 'product_id' => $variant->product_id]);

        $this->assertInstanceOf(ProductVariant::class, $cart->variant);
        $this->assertEquals($variant->id, $cart->variant->id);
    }

    /** @test */
    public function cart_belongs_to_a_product_directly_as_well()
    {
        // This tests the existing product_id link, ensuring it's still functional
        // even when variant_id is primary for specific items.
        $product = Product::factory()->create();
        $cart = Cart::factory()->forSimpleProduct()->create(['product_id' => $product->id, 'variant_id' => null]);

        $this->assertInstanceOf(Product::class, $cart->product);
        $this->assertEquals($product->id, $cart->product->id);
        $this->assertNull($cart->variant);
    }

    /** @test */
    public function cart_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $cart->user); // Assuming User model has 'carts' relationship or cart has 'user'
        $this->assertEquals($user->id, $cart->user->id);
    }

    /** @test */
    public function cart_can_belong_to_an_order()
    {
        // OrderFactory would be needed if not existing. For now, assume it can be created simply.
        $order = Order::factory()->create();
        $cart = Cart::factory()->create(['order_id' => $order->id]);

        $this->assertInstanceOf(Order::class, $cart->order);
        $this->assertEquals($order->id, $cart->order->id);
    }
}
