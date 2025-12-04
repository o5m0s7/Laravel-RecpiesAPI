<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->query('q');

        if (!$query) {
            return response()->json([
                'message' => 'Please provide a search keyword (?q=...)'
            ], 400);
        }

        $recipes = Recipe::where('title', 'LIKE', "%$query%")
            ->orWhere('category_name', 'LIKE', "%$query%")
            ->get();

        return response()->json([
            'q' => $query,
            'results' => $recipes
        ]);
    }
}
