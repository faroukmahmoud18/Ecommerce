<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;

class BrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('brands')->truncate();

        $brands = [
            'Apple', 'Samsung', 'Sony', 'LG', 'Nike', 'Adidas', 'Puma',
            'Dell', 'HP', 'Lenovo', 'Gucci', 'Prada', 'Zara', 'H&M',
            'Toyota', 'Honda', 'Ford', 'BMW', 'Mercedes-Benz', 'Audi'
        ];

        foreach ($brands as $brandName) {
            Brand::factory()->create(['title' => $brandName]);
        }

        // Create a few more using just the factory's default
        Brand::factory()->count(5)->create();
    }
}
