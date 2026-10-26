<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CategorySeeder::class);

        if (config('app.demo_mode', false)) {
            $this->call(DemoDataSeeder::class);
        }
    }
}
