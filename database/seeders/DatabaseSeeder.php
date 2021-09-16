<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

            // $this->call([
            //     RoleSeeder::class
            // ]);
            \App\Models\Product::factory(20)->create();
            \App\Models\ProductGallery::factory(60)->create();
    }
}
