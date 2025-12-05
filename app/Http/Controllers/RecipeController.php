<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\Step;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $categoryName = $request->query('category_name');
        $title = $request->query('title');

        $recipes = Recipe::query();

        if ($categoryName) {
            $recipes->where(function ($q) use ($categoryName) {
                $q->whereHas('category', function ($q2) use ($categoryName) {
                    $q2->where('name', 'LIKE', "%{$categoryName}%");
                });

                $q->orWhere('category_name', 'LIKE', "%{$categoryName}%");
            });
        }

        if ($title) {
            $recipes->where('title', 'LIKE', "%{$title}%");
        }

        return response()->json(
            $recipes->with(['category', 'ingredients', 'steps'])->get()
        );
    }


    public function show($id)
    {
        $recipe = Recipe::findOrFail($id)->load(['ingredients', 'steps']);
        return response()->json([$recipe], 200);
    }

    public function store(Request $request)
    {
        if (is_string($request->ingredients)) {
            $request->merge(['ingredients' => json_decode($request->ingredients, true)]);
        }
        if (is_string($request->steps)) {
            $request->merge(['steps' => json_decode($request->steps, true)]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cooking_time' => 'nullable|integer',
            'prep_time' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpg,png,jpeg',
            'category_name' => 'required|string|exists:categories,name',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.name' => 'required|string',
            'ingredients.*.quantity' => 'nullable|string',
            'steps' => 'required|array|min:1',
            'steps.*.description' => 'required|string',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('recipes', 'public');
        }
        $recipe = Recipe::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'cooking_time' => $validated['cooking_time'] ?? null,
            'prep_time' => $validated['prep_time'] ?? null,
            'category_name' => $validated['category_name'],
            'image_path' => $imagePath,
        ]);


        $stepNum = 1;
        foreach ($validated['steps'] as $step) {
            Step::create([
                'recipe_id' => $recipe->id,
                'step_number' => $stepNum++,
                'description' => $step['description'],
            ]);
        }

        foreach ($validated['ingredients'] as $item) {
            Ingredient::create([
                'recipe_id' => $recipe->id,
                'name' => $item['name'],
                'quantity' => $item['quantity'] ?? null,
            ]);
        }

        return response()->json($recipe->load(['ingredients', 'steps'], 201));
    }



    public function update(Request $request, $id)
    {
        $recipe = Recipe::findOrFail($id);

        if (is_string($request->ingredients)) {
            $request->merge(['ingredients' => json_decode($request->ingredients, true)]);
        }
        if (is_string($request->steps)) {
            $request->merge(['steps' => json_decode($request->steps, true)]);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cooking_time' => 'nullable|integer',
            'prep_time' => 'nullable|integer',
            'category_name' => 'required|string|exists:categories,name',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.name' => 'required|string',
            'ingredients.*.quantity' => 'nullable|string',
            'steps' => 'required|array|min:1',
            'steps.*.description' => 'required|string',
        ]);

        if ($request->hasFile('image')) {
            if ($recipe->image_path) Storage::disk('public')->delete($recipe->image_path);
            $recipe->image_path = $request->file('image')->store('recipes', 'public');
        }

        $recipe->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'cooking_time' => $validated['cooking_time'] ?? null,
            'prep_time' => $validated['prep_time'] ?? null,
            'category_name' => $validated['category_name'],
            'image_path' => $recipe->image_path,
        ]);

        Ingredient::where('recipe_id', $recipe->id)->delete();
        foreach ($validated['ingredients'] as $item) {
            Ingredient::create([
                'recipe_id' => $recipe->id,
                'name' => $item['name'],
                'quantity' => $item['quantity'] ?? null,
            ]);
        }

        Step::where('recipe_id', $recipe->id)->delete();
        $stepNum = 1;
        foreach ($validated['steps'] as $step) {
            Step::create([
                'recipe_id' => $recipe->id,
                'step_number' => $stepNum++,
                'description' => $step['description'],
            ]);
        }

        return response()->json($recipe->load(['ingredients', 'steps']));
    }



    public function destroy($id)
    {
        $recipe = Recipe::findOrFail($id);
        if ($recipe->image_path) Storage::disk('public')->delete($recipe->image_path);
        Ingredient::where('recipe_id', $recipe->id)->delete();
        Step::where('recipe_id', $recipe->id)->delete();
        $recipe->delete();
        return response()->json([
            'ID' => $id,
            'Title' => $recipe->title,
            'message' => 'Recipe deleted']);
    }
}
