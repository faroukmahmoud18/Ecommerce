<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->truncate(); // Clear existing categories and related child categories due to FK constraints if any without cascade.
                                           // Or handle deletion more carefully if there are strict FKs without ON DELETE CASCADE.

        // Main Categories
        $electronics = Category::factory()->create([
            'title' => 'Electronics',
            'summary' => 'All kinds of electronic gadgets and devices.',
            'is_parent' => true,
        ]);

        $fashion = Category::factory()->create([
            'title' => 'Fashion',
            'summary' => 'Clothing, apparel, and fashion accessories.',
            'is_parent' => true,
        ]);

        $home_garden = Category::factory()->create([
            'title' => 'Home & Garden',
            'summary' => 'Items for home, decor, and garden.',
            'is_parent' => true,
        ]);

        $books = Category::factory()->create([
            'title' => 'Books',
            'summary' => 'Various genres of books.',
            'is_parent' => true,
        ]);

        Category::factory()->count(3)->create(['is_parent' => true]); // Create 3 more main categories

        // Sub-categories for Electronics
        Category::factory()->create([
            'title' => 'Smartphones',
            'summary' => 'Latest mobile phones.',
            'is_parent' => false,
            'parent_id' => $electronics->id,
        ]);
        Category::factory()->create([
            'title' => 'Laptops',
            'summary' => 'Notebooks and ultrabooks.',
            'is_parent' => false,
            'parent_id' => $electronics->id,
        ]);
        Category::factory()->create([
            'title' => 'Audio Devices',
            'summary' => 'Headphones, speakers, etc.',
            'is_parent' => false,
            'parent_id' => $electronics->id,
        ]);

        // Sub-categories for Fashion
        Category::factory()->create([
            'title' => 'Men\'s Clothing',
            'summary' => 'Apparel for men.',
            'is_parent' => false,
            'parent_id' => $fashion->id,
        ]);
        Category::factory()->create([
            'title' => 'Women\'s Clothing',
            'summary' => 'Apparel for women.',
            'is_parent' => false,
            'parent_id' => $fashion->id,
        ]);
        Category::factory()->create([
            'title' => 'Footwear',
            'summary' => 'Shoes, sandals, boots.',
            'is_parent' => false,
            'parent_id' => $fashion->id,
        ]);

        // Sub-categories for Home & Garden
         Category::factory()->create([
            'title' => 'Furniture',
            'summary' => 'Home and office furniture.',
            'is_parent' => false,
            'parent_id' => $home_garden->id,
        ]);
        Category::factory()->create([
            'title' => 'Gardening Tools',
            'summary' => 'Tools for gardening.',
            'is_parent' => false,
            'parent_id' => $home_garden->id,
        ]);
    }
}
