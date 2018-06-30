<?php

namespace Tests\Feature;

use App\Http\Controllers\Recipe\RecipesController;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Cache;
use Illuminate\Support\Facades\DB;

class RecipesControllerTest extends TestCase
{
    const URL = 'http://api.tc-gousto.lh';

    const FIELDS = [
        'id',
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

    public function setUp()
    {
        parent::setUp();
        Cache::tags(RecipesController::CACHE_TAG_RECIPES)->flush();
    }

    public function tearDown()
    {
        DB::select('DELETE FROM recipes WHERE `id` > 10');
        DB::select('DELETE FROM rates');
        Cache::tags(RecipesController::CACHE_TAG_RECIPES)->flush();
        parent::tearDown();
    }

    public function providerGetAll()
    {
        return [
            'line_' . __LINE__ => [
                'url' => '/recipes',
                'ids' => [1, 2],
                'page' => 1,
            ],
            'line_' . __LINE__ => [
                'url' => '/recipes?page=2',
                'ids' => [3, 4],
                'page' => 2,
            ],
            'line_' . __LINE__ => [
                'url' => '/recipes?cuisine=asian',
                'ids' => [1, 6],
                'page' => 1,
            ],
            'line_' . __LINE__ => [
                'url' => '/recipes?cuisine=asian&page=2',
                'ids' => [],
                'page' => 2,
            ],
        ];
    }

    /**
     * @see \App\Http\Controllers\Recipe\RecipesController::getAll
     * @dataProvider providerGetAll
     */
    public function testGetAll($url, $ids, $page)
    {
        $response = $this->json('GET', self::URL . $url);
        $response->assertStatus(Response::HTTP_OK);

        $data = json_decode($response->getContent(), true);
        $this->assertTrue(isset($data['data']));
        $this->assertSame(count($ids), count($data['data']));
        $this->assertSame($page, $data['current_page']);

        foreach ($data['data'] as $k => $item) {
            $keys = array_keys($item);
            $this->assertSame(self::FIELDS, $keys);
            $this->assertSame($ids[$k], $item['id']);
        }
    }

    public function providerGetById()
    {
        return [
            'line_' . __LINE__ => [
                'url' => '/recipes/1',
                'id' => 1,
                'code' => Response::HTTP_OK,
            ],
            'line_' . __LINE__ => [
                'url' => '/recipes/100',
                'id' => null,
                'code' => Response::HTTP_NOT_FOUND,
            ],
        ];
    }

    /**
     * @see \App\Http\Controllers\Recipe\RecipesController::getById
     * @dataProvider providerGetById
     */
    public function testGetById($url, $id, $code)
    {
        $response = $this->json('GET', self::URL . $url);
        $response->assertStatus($code);
        $content = $response->getContent();

        if (!$id) {
            $this->assertSame('{}', $content);
            return;
        }

        $data = json_decode($content, true);

        $this->assertTrue(isset($data['id']));
        $this->assertSame($id, $data['id']);
        $keys = array_keys($data);
        $this->assertSame(self::FIELDS, $keys);
    }

    /**
     * @see \App\Http\Controllers\Recipe\RecipesController::create
     */
    public function testCreateError()
    {
        $response = $this->json(
            'POST',
            self::URL . '/recipes',
            [
                'box_type' => 'foo',
            ]
        );
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $errors = json_decode($response->getContent(), true);
        $this->assertSame(17, count($errors));

        $response = $this->json(
            'POST',
            self::URL . '/recipes',
            [
                'box_type' => 'vegetarian',
                'preparation_time_minutes' => 300,
            ]
        );
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $errors = json_decode($response->getContent(), true);
        $this->assertSame(16, count($errors));
    }

    /**
     * @see \App\Http\Controllers\Recipe\RecipesController::create
     */
    public function testCreateSuccess()
    {
        $response = $this->json(
            'POST',
            self::URL . '/recipes',
            [
                'box_type' => 'vegetarian',
                'title' => 'Foo Bar Pasta',
                'slug' => 'foo-bar-pasta',
                'calories_kcal' => 105,
                'protein_grams' => 110,
                'fat_grams' => 115,
                'carbs_grams' => 120,
                'recipe_diet_type_id' => 'vegetarian',
                'season' => 'all',
                'base' => 'noodles',
                'protein_source' => 'cheese',
                'preparation_time_minutes' => 25,
                'shelf_life_days' => 5,
                'equipment_needed' => 'Appetite',
                'origin_country_id' => 1,
                'recipe_cuisine' => 'italian',
                'gousto_reference' => 42,
            ]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $data = json_decode($response->getContent(), true);
        $this->assertTrue((int)$data['id'] > 0);
    }

    /**
     * @see \App\Http\Controllers\Recipe\RecipesController::update
     */
    public function testUpdateError()
    {
        $response = $this->json(
            'PUT',
            self::URL . '/recipes/1',
            [
                'box_type' => 'foo',
            ]
        );
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $errors = json_decode($response->getContent(), true);
        $this->assertSame(17, count($errors));

        $data = $this->createRecipe();
        $data['box_type'] = 'some';

        $response = $this->json('PUT', self::URL . '/recipes/' . $data['id'], $data);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @see \App\Http\Controllers\Recipe\RecipesController::update
     */
    public function testUpdateSuccess()
    {
        $data = $this->createRecipe();

        $data['box_type'] = 'gourmet';
        $data['title'] = 'Foo foo foo';
        $data['slug'] = 'foo-foo-foo';
        $data['calories_kcal'] = 200;
        $data['protein_grams'] = 201;
        $data['fat_grams'] = 202;
        $data['carbs_grams'] = 203;
        $data['base'] = 'pasta';
        $data['protein_source'] = 'seafood';
        $data['preparation_time_minutes'] = 20;
        $data['shelf_life_days'] = 10;
        $data['equipment_needed'] = 'None';
        $data['bulletpoint1'] = 'Some text';

        $response = $this->json('PUT', self::URL . '/recipes/' . $data['id'], $data);

        $data['bulletpoint2'] = null;
        $data['bulletpoint3'] = null;
        $data['in_your_box'] = '';
        $data['marketing_description'] = '';
        $data['short_title'] = null;

        $response->assertStatus(Response::HTTP_OK);
        $dataNew = json_decode($response->getContent(), true);

        ksort($data);
        ksort($dataNew);

        $this->assertSame($data, $dataNew);
    }

    /**
     * @see \App\Http\Controllers\Recipe\RecipesController::rate
     */
    public function testRate()
    {
        $response = $this->json('PUT', self::URL . '/recipes/42/rate', ['rate' => 5]);
        $response->assertStatus(Response::HTTP_NOT_FOUND);

        $data = $this->createRecipe();

        $response = $this->json('PUT', self::URL . '/recipes/' . $data['id'] . '/rate', ['rate' => 10]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $response = $this->json(
            'PUT',
            self::URL . '/recipes/' . $data['id'] . '/rate',
            ['rate' => 5]
        );
        $response->assertStatus(Response::HTTP_OK);
    }

    protected function createRecipe($data = []): array
    {
        $data = array_merge(
            [
                'box_type' => 'vegetarian',
                'title' => 'Foo Bar Pasta',
                'slug' => 'foo-bar-pasta',
                'calories_kcal' => 105,
                'protein_grams' => 110,
                'fat_grams' => 115,
                'carbs_grams' => 120,
                'recipe_diet_type_id' => 'vegetarian',
                'season' => 'all',
                'base' => 'noodles',
                'protein_source' => 'cheese',
                'preparation_time_minutes' => 25,
                'shelf_life_days' => 5,
                'equipment_needed' => 'Appetite',
                'origin_country_id' => 1,
                'recipe_cuisine' => 'italian',
                'gousto_reference' => 42,
            ],
            $data
        );
        $response = $this->json('POST', self::URL . '/recipes', $data);
        $response->assertStatus(Response::HTTP_CREATED);
        $data = json_decode($response->getContent(), true);
        $this->assertTrue((int)$data['id'] > 0);
        return $data;
    }

    public function testCache()
    {
        $Method = new \ReflectionMethod(RecipesController::class, 'getCacheKey');
        $Method->setAccessible(true);
        $cacheKey = $Method->invoke(null, "all:c:p1");

        Cache::tags(RecipesController::CACHE_TAG_RECIPES)->put($cacheKey, ['foo' => 'bar'], 50);

        $response = $this->json('GET', self::URL . '/recipes');
        $response->assertStatus(Response::HTTP_OK);
        $this->assertSame('{"foo":"bar"}', $response->getContent());

        $data = $this->createRecipe();
        $this->assertSame(null, Cache::get($cacheKey));

        Cache::tags(RecipesController::CACHE_TAG_RECIPES)->put($cacheKey, ['foo' => 'bar'], 50);

        $response = $this->json('GET', self::URL . '/recipes');
        $response->assertStatus(Response::HTTP_OK);
        $this->assertSame('{"foo":"bar"}', $response->getContent());

        $data['fat_grams'] = 200;
        $response = $this->json('PUT', self::URL . '/recipes/' . $data['id'], $data);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertSame(null, Cache::get($cacheKey));
    }
}
