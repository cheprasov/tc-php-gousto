<?php

use App\Model\Country;
use App\Model\Recipe\Recipe;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Country::truncate();
        Country::create(
            [
                'id' => 1,
                'title' => 'Great Britain',
            ]
        );

        Recipe::truncate();

        $csvFile = __DIR__ . '/../../resources/csv/recipe-data.csv';
        $csv = array_map('str_getcsv', file($csvFile));

        unset($csv[0]);
        if ($csv) {
            foreach ($csv as $i => $data) {
                $this->addRecipe($data);
            }
        }
    }

    protected function addRecipe($data)
    {
        Recipe::create(
            $a = [
                'id' => $data[0],
                'created_at' => preg_replace('/(\d{2})\/(\d{2})\/(\d{4})/', '$3-$2-$1', $data[1]),
                'updated_at' => preg_replace('/(\d{2})\/(\d{2})\/(\d{4})/', '$3-$2-$1', $data[2]),
                'box_type' => $data[3],
                'title' => $data[4],
                'slug' => $data[5],
                'short_title' => $data[6] ?: null,
                'marketing_description' => $data[7] ?: null,
                'calories_kcal' => (int)$data[8],
                'protein_grams' => (int)$data[9],
                'fat_grams' => (int)$data[10],
                'carbs_grams' => (int)$data[11],
                'bulletpoint1' => $data[12] ?: null,
                'bulletpoint2' => $data[13] ?: null,
                'bulletpoint3' => $data[14] ?: null,
                'recipe_diet_type_id' => $data[15],
                'season' => $data[16],
                'base' => $data[17] ?: null,
                'protein_source' => $data[18],
                'preparation_time_minutes' => (int)$data[19],
                'shelf_life_days' => (int)$data[20],
                'equipment_needed' => $data[21],
                'origin_country_id' => $data[22] = 1, // Great Britain
                'recipe_cuisine' => $data[23],
                'in_your_box' => $data[24] ?: '',
                'gousto_reference' => (int)$data[25],
            ]
        );
    }
}
