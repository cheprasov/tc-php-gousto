<?php

namespace App\Model\Recipe;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $table = 'recipes';

    protected $perPage = 2;

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'box_type',
        'title',
        'slug',
        'short_title',
        'marketing_description',
        'calories_kcal',
        'protein_grams',
        'fat_grams',
        'carbs_grams',
        'bulletpoint1',
        'bulletpoint2',
        'bulletpoint3',
        'recipe_diet_type_id',
        'season',
        'base',
        'protein_source',
        'preparation_time_minutes',
        'shelf_life_days',
        'equipment_needed',
        'origin_country_id',
        'recipe_cuisine',
        'in_your_box',
        'gousto_reference',
    ];
}
