<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Recipe\RecipesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::domain(config('app.domain_api'))->group(function () {
    // Fetch all recipes for a specific cuisine (should paginate)
    Route::get('/recipes', RecipesController::getRouteMethod('getAll'));

    // Fetch a recipe by id
    Route::get('/recipes/{recipe_id}', RecipesController::getRouteMethod('getById'));

    // Store a new recipe
    Route::post('/recipes', RecipesController::getRouteMethod('create'));

    // Update an existing recipe
    Route::put('/recipes/{recipe_id}', RecipesController::getRouteMethod('update'));

    // Rate an existing recipe between 1 and 5
    Route::put('/recipes/{recipe_id}/rate', RecipesController::getRouteMethod('rate'));
});




