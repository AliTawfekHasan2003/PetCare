<?php

namespace Database\Seeders;

use App\Models\Breed;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;

class BreedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Breed::truncate();
        Schema::enableForeignKeyConstraints();

        Breed::create([
            'name' => 'Golden Retriever',
            'category_id' => 1,
        ]);

        Breed::create([
            'name' => 'German Shepherd',
            'category_id' => 1,
        ]);

        Breed::create([
            'name' => 'Beagle',
            'category_id' => 1,
        ]);

        Breed::create([
            'name' => 'Persian',
            'category_id' => 2,
        ]);

        Breed::create([
            'name' => 'Siamese',
            'category_id' => 2,
        ]);

        Breed::create([
            'name' => 'Maine Coon',
            'category_id' => 2,
        ]);

        Breed::create([
            'name' => 'Budgerigar',
            'category_id' => 3,
        ]);

        Breed::create([
            'name' => 'Cockatiel',
            'category_id' => 3,
        ]);

        Breed::create([
            'name' => 'African Grey',
            'category_id' => 3,
        ]);

        Breed::create([
            'name' => 'Bearded Dragon',
            'category_id' => 4,
        ]);

        Breed::create([
            'name' => 'Ball Python',
            'category_id' => 4,
        ]);

        Breed::create([
            'name' => 'Leopard Gecko',
            'category_id' => 4,
        ]);
    }
}
