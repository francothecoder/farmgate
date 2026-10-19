<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Crops and Grains',
            'Vegetables',
            'Fruits',
            'Livestock',
            'Poultry',
            'Agricultural Inputs',
            'Farm Equipment',
            'Irrigation Equipment',
            'Animal Feed',
            'Agricultural Services',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate([
                'slug' => str($name)->slug()->toString(),
            ], ['name' => $name]);
        }
    }
}
