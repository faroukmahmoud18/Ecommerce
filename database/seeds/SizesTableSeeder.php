<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Size;
use Illuminate\Support\Facades\DB;

class SizesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sizes')->truncate();

        $sizes = [
            ['name' => 'XS (Extra Small)'],
            ['name' => 'S (Small)'],
            ['name' => 'M (Medium)'],
            ['name' => 'L (Large)'],
            ['name' => 'XL (Extra Large)'],
            ['name' => 'XXL (Double Extra Large)'],
            ['name' => '30 inches'],
            ['name' => '32 inches'],
            ['name' => '34 inches'],
            ['name' => '36 inches'],
            ['name' => 'One Size Fits All'],
            ['name' => 'N/A'], // For products where size is not applicable
        ];

        foreach ($sizes as $size) {
            Size::create($size);
        }
    }
}
