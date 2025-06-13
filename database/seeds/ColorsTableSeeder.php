<?php

namespace Database\Seeders; // Assuming this namespace based on modern Laravel structure

use Illuminate\Database\Seeder;
use App\Models\Color; // Assuming model namespace
use Illuminate\Support\Facades\DB; // For potential direct DB operations if needed

class ColorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('colors')->truncate(); // Clear existing data if desired

        $colors = [
            ['name' => 'Red', 'hex_code' => '#FF0000'],
            ['name' => 'Blue', 'hex_code' => '#0000FF'],
            ['name' => 'Green', 'hex_code' => '#008000'],
            ['name' => 'Black', 'hex_code' => '#000000'],
            ['name' => 'White', 'hex_code' => '#FFFFFF'],
            ['name' => 'Silver', 'hex_code' => '#C0C0C0'],
            ['name' => 'Gold', 'hex_code' => '#FFD700'],
            ['name' => 'Yellow', 'hex_code' => '#FFFF00'],
            ['name' => 'Orange', 'hex_code' => '#FFA500'],
            ['name' => 'Purple', 'hex_code' => '#800080'],
            ['name' => 'Pink', 'hex_code' => '#FFC0CB'],
            ['name' => 'Brown', 'hex_code' => '#A52A2A'],
            ['name' => 'Gray', 'hex_code' => '#808080'],
            ['name' => 'Beige', 'hex_code' => '#F5F5DC'],
            ['name' => 'Turquoise', 'hex_code' => '#40E0D0'],
        ];

        foreach ($colors as $color) {
            Color::create($color);
        }
    }
}
