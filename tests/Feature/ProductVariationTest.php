<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Color;
use App\Models\Size;
use App\Models\Specification;
use App\Models\Category;
use App\Models\Brand;

class ProductVariationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin user for authentication
        $this->admin = User::factory()->create(['role' => 'admin']); // Assuming 'admin' role logic exists

        // Ensure necessary base data like categories and brands can be created
        Category::factory()->create(); // Ensure at least one category
        Brand::factory()->create();    // Ensure at least one brand
        Color::factory()->create(['name' => 'Red']); // Ensure some base attributes exist
        Size::factory()->create(['name' => 'M']);
        Specification::factory()->create(['name' => 'Material', 'value' => 'Cotton']);
    }

    /** @test */
    public function admin_can_access_create_product_page()
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('product.create'));
        $response->assertStatus(200);
        $response->assertViewIs('backend.product.create');
    }

    /** @test */
    public function admin_can_create_product_with_variations()
    {
        $this->actingAs($this->admin, 'web');

        $category = Category::first();
        $brand = Brand::first();
        $color = Color::where('name', 'Red')->first();
        $size = Size::where('name', 'M')->first();
        $spec = Specification::where('name', 'Material')->first();

        $productData = [
            'title' => 'Test Product with Variants',
            'summary' => 'Summary of test product.',
            'description' => 'Detailed description.',
            'photo' => '/uploads/default.png', // Placeholder
            'stock' => 100, // Main product stock, controller might ignore this if all stock is in variants
            'cat_id' => $category->id,
            'brand_id' => $brand->id,
            'price' => 99.99, // Main product price
            'status' => 'active',
            'condition' => 'new',
            'is_featured' => 1,
            'variants' => [
                [
                    'color_id' => $color->id,
                    'size_id' => $size->id,
                    'specification_id' => null,
                    'price' => 100.00,
                    'stock' => 10,
                    'sku' => 'VAR-RED-M-01'
                ],
                [
                    'color_id' => null,
                    'size_id' => $size->id,
                    'specification_id' => $spec->id,
                    'price' => 105.00,
                    'stock' => 5,
                    'sku' => 'VAR-M-COTTON-02'
                ]
            ]
        ];

        $response = $this->post(route('product.store'), $productData);

        $response->assertRedirect(route('product.index'));
        $response->assertSessionHas('success', 'Product Successfully added');

        $this->assertDatabaseHas('products', ['title' => 'Test Product with Variants']);
        $product = Product::where('title', 'Test Product with Variants')->first();
        $this->assertNotNull($product);

        $this->assertDatabaseCount('product_variants', 2);
        $this->assertDatabaseHas('product_variants', [
            'product_id' => $product->id,
            'sku' => 'VAR-RED-M-01',
            'price' => 100.00,
            'stock' => 10,
            'color_id' => $color->id,
            'size_id' => $size->id,
        ]);
        $this->assertDatabaseHas('product_variants', [
            'product_id' => $product->id,
            'sku' => 'VAR-M-COTTON-02',
            'price' => 105.00,
            'stock' => 5,
            'size_id' => $size->id,
            'specification_id' => $spec->id,
        ]);

        // Check pivot table entries
        $this->assertDatabaseHas('product_color', ['product_id' => $product->id, 'color_id' => $color->id]);
        $this->assertDatabaseHas('product_size', ['product_id' => $product->id, 'size_id' => $size->id]);
        // For the second variant, color_id was null, so no new product_color entry for it.
        // Size M was used in both, so only one product_size entry for M.
        $this->assertEquals(1, $product->sizes()->count());
        $this->assertDatabaseHas('product_specification', ['product_id' => $product->id, 'specification_id' => $spec->id]);
    }

    /** @test */
    public function admin_can_access_edit_product_page_with_variations()
    {
        $product = Product::factory()
            ->has(ProductVariant::factory()->count(2)->withColor()->withSize(), 'variants')
            ->create();

        $response = $this->actingAs($this->admin, 'web')->get(route('product.edit', $product->id));
        $response->assertStatus(200);
        $response->assertViewIs('backend.product.edit');
        $response->assertViewHas('product', function ($viewProduct) use ($product) {
            return $viewProduct->id === $product->id && $viewProduct->variants->count() === 2;
        });
        $response->assertViewHas('colors');
        $response->assertViewHas('sizes');
        $response->assertViewHas('specifications');
    }

    /** @test */
    public function admin_can_update_product_variants_price_stock_sku()
    {
        $this->actingAs($this->admin, 'web');
        $color = Color::first();
        $size = Size::first();

        $product = Product::factory()->create();
        $variant1 = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'size_id' => $size->id,
            'price' => 50,
            'stock' => 10,
            'sku' => 'OLD-SKU-01'
        ]);
        $variant2 = ProductVariant::factory()->create(['product_id' => $product->id, 'price' => 60, 'stock' => 20, 'sku' => 'OLD-SKU-02']);

        $updatedVariantData = [
            'title' => $product->title, // Required by validation
            'summary' => $product->summary,
            'photo' => $product->photo,
            'stock' => $product->stock,
            'cat_id' => $product->cat_id,
            'price' => $product->price,
            'status' => $product->status,
            'condition' => $product->condition,
            'variants' => [
                [ // Update variant1
                    'id' => $variant1->id,
                    'color_id' => $variant1->color_id,
                    'size_id' => $variant1->size_id,
                    'specification_id' => $variant1->specification_id,
                    'price' => 55.50,
                    'stock' => 8,
                    'sku' => 'NEW-SKU-01'
                ],
                [ // Keep variant2, but can also be updated
                    'id' => $variant2->id,
                    'color_id' => $variant2->color_id,
                    'size_id' => $variant2->size_id,
                    'specification_id' => $variant2->specification_id,
                    'price' => $variant2->price,
                    'stock' => $variant2->stock,
                    'sku' => $variant2->sku
                ]
            ]
        ];

        $response = $this->patch(route('product.update', $product->id), $updatedVariantData);
        $response->assertRedirect(route('product.index'));
        $response->assertSessionHas('success', 'Product Successfully updated');

        $this->assertDatabaseHas('product_variants', [
            'id' => $variant1->id,
            'price' => 55.50,
            'stock' => 8,
            'sku' => 'NEW-SKU-01'
        ]);
    }

    /** @test */
    public function admin_can_add_new_variant_to_existing_product()
    {
        $this->actingAs($this->admin, 'web');
        $product = Product::factory()->has(ProductVariant::factory()->count(1), 'variants')->create();
        $existingVariant = $product->variants->first();

        $color = Color::first(); // Use existing or create new
        $size = Size::first();

        $updateData = [
            'title' => $product->title, // Required fields for product update
            'summary' => $product->summary,
            'photo' => $product->photo,
            'stock' => $product->stock,
            'cat_id' => $product->cat_id,
            'price' => $product->price,
            'status' => $product->status,
            'condition' => $product->condition,
            'variants' => [
                [ // Existing variant
                    'id' => $existingVariant->id,
                    'color_id' => $existingVariant->color_id,
                    'size_id' => $existingVariant->size_id,
                    'specification_id' => $existingVariant->specification_id,
                    'price' => $existingVariant->price,
                    'stock' => $existingVariant->stock,
                    'sku' => $existingVariant->sku,
                ],
                [ // New variant
                    'color_id' => $color->id,
                    'size_id' => $size->id,
                    'specification_id' => null,
                    'price' => 120.00,
                    'stock' => 15,
                    'sku' => 'NEW-VARIANT-SKU'
                ]
            ]
        ];

        $this->patch(route('product.update', $product->id), $updateData);

        $this->assertDatabaseCount('product_variants', 2);
        $this->assertDatabaseHas('product_variants', [
            'product_id' => $product->id,
            'sku' => 'NEW-VARIANT-SKU',
            'price' => 120.00,
            'stock' => 15
        ]);
         // Check pivot tables are updated (color and size from new variant should be attached to product)
        $product->refresh();
        $this->assertTrue($product->colors->contains($color));
        $this->assertTrue($product->sizes->contains($size));
    }

    /** @test */
    public function admin_can_remove_variant_from_existing_product()
    {
        $this->actingAs($this->admin, 'web');
        $product = Product::factory()->create();
        $variantToKeep = ProductVariant::factory()->create(['product_id' => $product->id, 'sku' => 'KEEP-ME']);
        $variantToRemove = ProductVariant::factory()->create(['product_id' => $product->id, 'sku' => 'DELETE-ME', 'color_id' => Color::first()->id]);

        // Attach color from variantToRemove to ensure pivot update logic is tested
        $product->colors()->attach($variantToRemove->color_id);


        $updateData = [
            'title' => $product->title, // Required fields for product update
            'summary' => $product->summary,
            'photo' => $product->photo,
            'stock' => $product->stock,
            'cat_id' => $product->cat_id,
            'price' => $product->price,
            'status' => $product->status,
            'condition' => $product->condition,
            'variants' => [
                [ // Variant to keep
                    'id' => $variantToKeep->id,
                    'color_id' => $variantToKeep->color_id,
                    'size_id' => $variantToKeep->size_id,
                    'specification_id' => $variantToKeep->specification_id,
                    'price' => $variantToKeep->price,
                    'stock' => $variantToKeep->stock,
                    'sku' => $variantToKeep->sku,
                ]
                // Variant to remove is omitted from the array
            ]
        ];

        $this->patch(route('product.update', $product->id), $updateData);

        $this->assertDatabaseCount('product_variants', 1);
        $this->assertDatabaseHas('product_variants', ['sku' => 'KEEP-ME']);
        $this->assertDatabaseMissing('product_variants', ['sku' => 'DELETE-ME']);

        // Check if color associated ONLY with the removed variant is detached from product
        // This requires careful setup; if variantToKeep also had this color, it would still be attached.
        // For this test, assume variantToRemove->color_id was unique or variantToKeep has a different/no color.
        if ($variantToKeep->color_id != $variantToRemove->color_id) {
             $product->refresh(); // reload relationships
             $this->assertFalse($product->colors->contains($variantToRemove->color_id));
        }
    }

    /** @test */
    public function admin_deleting_product_also_deletes_variants_and_pivot_entries()
    {
        $this->actingAs($this->admin, 'web');

        $color = Color::first();
        $size = Size::first();

        $product = Product::factory()->create();
        ProductVariant::factory()->create(['product_id' => $product->id, 'color_id' => $color->id, 'size_id' => $size->id]);

        $product->colors()->attach($color->id);
        $product->sizes()->attach($size->id);

        $productId = $product->id;
        $variantId = $product->variants->first()->id;

        $this->assertDatabaseHas('products', ['id' => $productId]);
        $this->assertDatabaseHas('product_variants', ['id' => $variantId]);
        $this->assertDatabaseHas('product_color', ['product_id' => $productId, 'color_id' => $color->id]);
        $this->assertDatabaseHas('product_size', ['product_id' => $productId, 'size_id' => $size->id]);

        $response = $this->delete(route('product.destroy', $product->id));

        $response->assertRedirect(route('product.index'));
        $response->assertSessionHas('success', 'Product successfully deleted');

        $this->assertDatabaseMissing('products', ['id' => $productId]);
        $this->assertDatabaseMissing('product_variants', ['id' => $variantId]);
        $this->assertDatabaseMissing('product_color', ['product_id' => $productId]); // Pivot entries should be gone due to cascade
        $this->assertDatabaseMissing('product_size', ['product_id' => $productId]);
    }

    // Frontend Tests
    /** @test */
    public function product_detail_page_loads_with_variant_data()
    {
        $color = Color::factory()->create(['name' => 'Green']);
        $size = Size::factory()->create(['name' => 'XL']);
        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id,
            'size_id' => $size->id,
            'price' => 123.45,
            'stock' => 5,
        ]);

        // Manually associate with pivot tables for complete data mirroring controller logic
        $product->colors()->attach($color->id);
        $product->sizes()->attach($size->id);


        $response = $this->get(route('product-detail', $product->slug));
        $response->assertStatus(200);
        $response->assertSee($product->title);
        // Check if variant data is embedded (approximation, actual structure depends on blade)
        $response->assertSeeText(htmlspecialchars(json_encode($variant->id), ENT_QUOTES)); // Check for variant ID
        $response->assertSeeText(htmlspecialchars(json_encode($variant->price), ENT_QUOTES));
        $response->assertSeeText(htmlspecialchars(json_encode($color->name), ENT_QUOTES));
        $response->assertSeeText(htmlspecialchars(json_encode($size->name), ENT_QUOTES));

        // Check if the main product price is displayed (it might be the base or a default variant's price)
        // The exact assertion depends on how the blade template handles initial price display
        // For now, let's ensure it contains some form of price information related to the product or variant.
        $response->assertSeeText(number_format($product->price,2)); // Base price or initial variant price
    }

    /** @test */
    public function user_can_add_specific_variant_to_cart()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'price' => 77.88,
            'stock' => 3,
        ]);

        $response = $this->post(route('single-add-to-cart'), [
            'variant_id' => $variant->id,
            'quant' => [1 => 1] // Assuming this structure from CartController
        ]);

        $response->assertRedirect(); // Should redirect back or to cart
        $response->assertSessionHas('success', 'Product successfully added to cart.');

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'quantity' => 1,
            'price' => 77.88,
            'amount' => 77.88
        ]);
    }

    /** @test */
    public function adding_same_variant_to_cart_increments_quantity()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'price' => 50.00,
            'stock' => 10,
        ]);

        // Add first time
        $this->post(route('single-add-to-cart'), ['variant_id' => $variant->id, 'quant' => [1 => 1]]);
        // Add second time
        $this->post(route('single-add-to-cart'), ['variant_id' => $variant->id, 'quant' => [1 => 2]]);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'variant_id' => $variant->id,
            'quantity' => 3, // 1 + 2
            'amount' => 50.00 * 3,
        ]);
        $this->assertDatabaseCount('carts', 1); // Should still be one cart record for this variant
    }

    /** @test */
    public function cannot_add_out_of_stock_variant_to_cart()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $product = Product::factory()->create();
        $variant = ProductVariant::factory()->create([
            'product_id' => $product->id,
            'stock' => 1, // Only 1 in stock
        ]);

        // Add 1 successfully
        $this->post(route('single-add-to-cart'), ['variant_id' => $variant->id, 'quant' => [1 => 1]]);

        // Try to add 1 more
        $response = $this->post(route('single-add-to-cart'), ['variant_id' => $variant->id, 'quant' => [1 => 1]]);

        $response->assertSessionHas('error', 'Stock not sufficient for the selected variant!. Max available: ' . $variant->stock);
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'variant_id' => $variant->id,
            'quantity' => 1, // Should remain 1
        ]);
    }
     /** @test */
    public function cannot_add_more_than_variant_stock_to_cart()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $variant = ProductVariant::factory()->create(['stock' => 3]);

        $response = $this->post(route('single-add-to-cart'), [
            'variant_id' => $variant->id,
            'quant' => [1 => 4] // Requesting 4, stock is 3
        ]);

        $response->assertSessionHas('error'); // General error for out of stock
        $this->assertDatabaseMissing('carts', [ // No cart item should be created or quantity should not be 4
            'user_id' => $user->id,
            'variant_id' => $variant->id,
            'quantity' => 4
        ]);
         $this->assertDatabaseCount('carts', 0); // ensure cart is empty
    }
}
