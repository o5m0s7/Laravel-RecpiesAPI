<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_recipe_with_json()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test Category', 'slug' => 'test-category']);

        $data = [
            'title' => 'Test Recipe',
            'description' => 'Test Description',
            'cooking_time' => 30,
            'category_id' => $category->id,
            'ingredients' => [
                ['name' => 'Salt', 'quantity' => '1 tsp'],
                ['name' => 'Pepper', 'quantity' => '1 pinch'],
            ],
            'steps' => [
                ['description' => 'Step 1'],
                ['description' => 'Step 2'],
            ],
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/recipes', $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('recipes', ['title' => 'Test Recipe']);
        $this->assertDatabaseHas('ingredients', ['name' => 'Salt']);
        $this->assertDatabaseHas('steps', ['description' => 'Step 1']);
    }

    public function test_can_create_recipe_with_image()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Test Category 2', 'slug' => 'test-category-2']);
        $file = UploadedFile::fake()->image('recipe.jpg');

        $data = [
            'title' => 'Test Recipe Image',
            'description' => 'Test Description',
            'cooking_time' => 45,
            'category_id' => $category->id,
            'image' => $file,
            'ingredients' => [
                ['name' => 'Salt', 'quantity' => '1 tsp'],
            ],
            'steps' => [
                ['description' => 'Step 1'],
            ],
        ];

        // When sending files, we usually use multipart/form-data.
        // The post method in Laravel tests handles this automatically if files are present.
        // However, we need to be careful with array data in multipart.
        // Laravel's test helper handles array data correctly.
        
        $response = $this->actingAs($user)
            ->post('/api/recipes', $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('recipes', ['title' => 'Test Recipe Image']);
        Storage::disk('public')->assertExists('recipes/' . $file->hashName());
    }
}
