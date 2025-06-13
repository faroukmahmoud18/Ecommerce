<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Specification;
use Illuminate\Support\Facades\DB;

class SpecificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('specifications')->truncate();

        $specifications = [
            // Material
            ['name' => 'Material', 'value' => 'Cotton'],
            ['name' => 'Material', 'value' => 'Polyester'],
            ['name' => 'Material', 'value' => 'Silk'],
            ['name' => 'Material', 'value' => 'Wool'],
            ['name' => 'Material', 'value' => 'Leather'],
            ['name' => 'Material', 'value' => 'Denim'],
            ['name' => 'Material', 'value' => 'Aluminum'],
            ['name' => 'Material', 'value' => 'Plastic'],
            ['name' => 'Material', 'value' => 'Glass'],
            ['name' => 'Material', 'value' => 'Wood'],

            // Storage (for electronics)
            ['name' => 'Storage', 'value' => '64GB'],
            ['name' => 'Storage', 'value' => '128GB'],
            ['name' => 'Storage', 'value' => '256GB'],
            ['name' => 'Storage', 'value' => '512GB'],
            ['name' => 'Storage', 'value' => '1TB'],
            ['name' => 'Storage', 'value' => '2TB'],

            // RAM (for electronics)
            ['name' => 'RAM', 'value' => '4GB'],
            ['name' => 'RAM', 'value' => '8GB'],
            ['name' => 'RAM', 'value' => '16GB'],
            ['name' => 'RAM', 'value' => '32GB'],
            ['name' => 'RAM', 'value' => '64GB'],

            // Screen Size (for electronics)
            ['name' => 'Screen Size', 'value' => '5 inches'],
            ['name' => 'Screen Size', 'value' => '5.5 inches'],
            ['name' => 'Screen Size', 'value' => '6 inches'],
            ['name' => 'Screen Size', 'value' => '6.7 inches'],
            ['name' => 'Screen Size', 'value' => '10 inches (Tablet)'],
            ['name' => 'Screen Size', 'value' => '13 inches (Laptop)'],
            ['name' => 'Screen Size', 'value' => '15 inches (Laptop)'],
            ['name' => 'Screen Size', 'value' => '24 inches (Monitor)'],
            ['name' => 'Screen Size', 'value' => '27 inches (Monitor)'],
            ['name' => 'Screen Size', 'value' => '55 inches (TV)'],
            ['name' => 'Screen Size', 'value' => '65 inches (TV)'],

            // Power Source
            ['name' => 'Power Source', 'value' => 'Battery Powered'],
            ['name' => 'Power Source', 'value' => 'USB Powered'],
            ['name' => 'Power Source', 'value' => 'AC Adapter'],
            ['name' => 'Power Source', 'value' => 'Solar Powered'],
        ];

        foreach ($specifications as $spec) {
            // Using firstOrCreate to avoid issues if a name/value pair is duplicated by chance or design
            Specification::firstOrCreate(['name' => $spec['name'], 'value' => $spec['value']]);
        }
    }
}
