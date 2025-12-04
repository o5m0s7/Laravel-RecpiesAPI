<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\Step;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        foreach (Recipe::all() as $recipe) {
            Step::factory(3)->create([
                'recipe_id' => $recipe->id
            ]);
        }
    }
}
