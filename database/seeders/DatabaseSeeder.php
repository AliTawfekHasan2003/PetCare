<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TranslationsLanguageSeeder::class,
            CountrySeeder::class,            
            PermissionSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            BreedSeeder::class,
        ]);
    }
}
