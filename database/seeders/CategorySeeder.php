<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Category::truncate();
        Schema::enableForeignKeyConstraints();
        
        Category::create([
            'id' => 1,
            'name' => 'Dogs',
        ]);

        Category::create([
            'id' => 2,
            'name' => 'Cats',
        ]);

        Category::create([
            'id' => 3,
            'name' => 'Brids',
        ]);

        Category::create([
            'id' => 4,
            'name' => 'Reptiles',
        ]);
    }
}
