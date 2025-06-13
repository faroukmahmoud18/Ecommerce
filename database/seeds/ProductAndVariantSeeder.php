<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Color;
use App\Models\Size;
use App\Models\Specification;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Str; // Added import for Str

class ProductAndVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Optional: Truncate product_variants, product_color, product_size, product_specification, products
        // Be careful with truncate if other seeders depend on products without running this one last.
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DB::table('product_variants')->truncate();
        // DB::table('product_color')->truncate();
        // DB::table('product_size')->truncate();
        // DB::table('product_specification')->truncate();
        // DB::table('products')->truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $colors = Color::all();
        $sizes = Size::all();
        $specifications = Specification::all();
        $categories = Category::where('is_parent', false)->get(); // Use sub-categories for products
        if ($categories->isEmpty()) {
            $categories = Category::where('is_parent', true)->get(); // Fallback to parent if no sub-categories
        }
        if ($categories->isEmpty()) {
             $this->command->getOutput()->writeln("<error>No categories found. Please seed categories first.</error>");
             return; // Cannot proceed without categories
        }

        $brands = Brand::all();
         if ($brands->isEmpty()) {
             $this->command->getOutput()->writeln("<error>No brands found. Please seed brands first.</error>");
             return; // Cannot proceed without brands
        }


        Product::factory()->count(25)->create()->each(function ($product) use ($colors, $sizes, $specifications) {
            $numberOfVariants = rand(2, 5);
            $usedCombinations = []; // To avoid exact same variant for the same product

            $productColorIds = collect();
            $productSizeIds = collect();
            $productSpecificationIds = collect();

            for ($i = 0; $i < $numberOfVariants; $i++) {
                $variantAttributes = [];
                $combinationKey = '';

                // Randomly decide if this variant will have color, size, spec
                $hasColor = $colors->isNotEmpty() && (rand(0, 1) || $i == 0); // At least first variant tries to have color
                $hasSize = $sizes->isNotEmpty() && (rand(0, 1) || $i == 0);
                $hasSpec = $specifications->isNotEmpty() && rand(0, 1);

                if ($hasColor) {
                    $color = $colors->random();
                    $variantAttributes['color_id'] = $color->id;
                    $combinationKey .= 'c' . $color->id;
                    $productColorIds->push($color->id);
                }
                if ($hasSize) {
                    $size = $sizes->random();
                    $variantAttributes['size_id'] = $size->id;
                    $combinationKey .= 's' . $size->id;
                    $productSizeIds->push($size->id);
                }
                if ($hasSpec) {
                    $spec = $specifications->random();
                    $variantAttributes['specification_id'] = $spec->id;
                    $combinationKey .= 'p' . $spec->id;
                    $productSpecificationIds->push($spec->id);
                }

                // Ensure at least one attribute is selected if possible, or skip if all are empty by chance
                if(empty($combinationKey) && ($colors->isNotEmpty() || $sizes->isNotEmpty() || $specifications->isNotEmpty())) {
                    // If by chance all were skipped but attributes exist, try adding at least one
                    if ($colors->isNotEmpty()) {
                         $color = $colors->random();
                         $variantAttributes['color_id'] = $color->id;
                         $combinationKey .= 'c' . $color->id;
                         $productColorIds->push($color->id);
                    } elseif($sizes->isNotEmpty()){
                         $size = $sizes->random();
                         $variantAttributes['size_id'] = $size->id;
                         $combinationKey .= 's' . $size->id;
                         $productSizeIds->push($size->id);
                    }
                    // No need to add spec here again if it was skipped
                }


                if (empty($combinationKey) || isset($usedCombinations[$combinationKey])) {
                    // Skip if combination is empty (no attributes assigned) or already used for this product
                    // This might result in fewer than $numberOfVariants if unique combinations are exhausted quickly
                    continue;
                }
                $usedCombinations[$combinationKey] = true;

                // Adjust price based on product's base price
                $variantPrice = $product->price + rand(-10, 20);
                if ($variantPrice <= 0) $variantPrice = $product->price > 0 ? $product->price / 2 : 10;


                ProductVariant::factory()->create(array_merge(
                    $variantAttributes,
                    [
                        'product_id' => $product->id,
                        'price' => $variantPrice,
                        'stock' => rand(5, 50),
                        'sku' => $product->slug . '-' . strtoupper(Str::random(4)),
                    ]
                ));
            }

            // Sync pivot tables for the product with all unique attributes from its variants
            if ($productColorIds->isNotEmpty()) {
                $product->colors()->syncWithoutDetaching($productColorIds->unique()->toArray());
            }
            if ($productSizeIds->isNotEmpty()) {
                $product->sizes()->syncWithoutDetaching($productSizeIds->unique()->toArray());
            }
            if ($productSpecificationIds->isNotEmpty()) {
                $product->specifications()->syncWithoutDetaching($productSpecificationIds->unique()->toArray());
            }
        });
    }
}
