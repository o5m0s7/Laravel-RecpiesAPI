<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('register', [UserController::class, 'register']);
Route::post('login',    [UserController::class, 'login']);


Route::get('recipes',          [RecipeController::class, 'index']);
Route::get('recipes/{id}',     [RecipeController::class, 'show']);

Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{id}', [CategoryController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);

    // Recipes
    // Route::apiResource('recipes', RecipeController::class);
    Route::post('/recipes',         [RecipeController::class, 'store']);
    Route::put('/recipes/{id}',     [RecipeController::class, 'update']);
    Route::delete('/recipes/{id}',  [RecipeController::class, 'destroy']);


    // Category
    // Route::apiResource('categories', CategoryController::class);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

});

    





