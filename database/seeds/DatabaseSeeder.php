<?php

namespace Database\Seeders; // Added namespace

use Illuminate\Database\Seeder;
// Need to ensure all called seeders are either in the same namespace
// or fully qualified if they are in the global namespace or different one.
// Assuming all custom seeders are now in Database\Seeders.
// For existing ones like SettingTableSeeder, CouponSeeder, if they are global,
// they might need to be called as \SettingTableSeeder::class

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // It's good practice to ensure seeders are idempotent or data is reset if needed.
        // Individual seeders now have truncate, so order here is mainly for dependency.

        $this->call(UsersTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(BrandsTableSeeder::class);
        $this->call(ColorsTableSeeder::class);
        $this->call(SizesTableSeeder::class);
        $this->call(SpecificationsTableSeeder::class);

        // ProductAndVariantSeeder depends on the above seeders
        $this->call(ProductAndVariantSeeder::class);

        // Other existing seeders - if these are in global namespace, they need a leading \
        // For now, assuming they are also updated or should be in Database\Seeders
        // If they are old and in global, it would be:
        // $this->call(\SettingTableSeeder::class);
        // $this->call(\CouponSeeder::class);
        // For consistency, it's better if all are namespaced.
        // Assuming for now these are also in Database\Seeders or will be updated.
        $this->call(SettingTableSeeder::class);
        $this->call(CouponSeeder::class);
    }
}
