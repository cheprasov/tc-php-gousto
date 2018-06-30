<?php

namespace App\Http\Controllers\Recipe;

use App\Http\Controllers\Controller;
use App\Model\Recipe\Rate;
use Illuminate\Http\Request;
use App\Model\Recipe\Recipe;
use Illuminate\Validation\Rule;
use Validator as ValidatorFacade;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use Cache;

class RecipesController extends Controller
{
    const CACHE_TAG_RECIPES = 'recipes:all';
    const CACHE_PUT_MIN = 600;

    /**
     * @param string $str
     * @return string
     */
    protected static function getCacheKey(string $str): string
    {
        return 'cache:recipes:' . $str;
    }

    /**
     * @param Request $Request
     * @return Validator
     */
    protected function getValidator(Request $Request): Validator
    {
        return ValidatorFacade::make(
            $Request->input(),
            [
                'box_type' => [
                    'required',
                    Rule::In(['gourmet', 'vegetarian']),
                ],
                'title' => [
                    'required'
                ],
                'slug' => [
                    'required',
                    'regex:/^[-a-z0-9]+$/'
                ],
                'calories_kcal' => [
                    'required',
                    'integer',
                    'min:0',
                ],
                'protein_grams' => [
                    'required',
                    'integer',
                    'min:0',
                ],
                'fat_grams' => [
                    'required',
                    'integer',
                    'min:0',
                ],
                'carbs_grams' => [
                    'required',
                    'integer',
                    'min:0',
                ],
                'recipe_diet_type_id' => [
                    'required',
                    Rule::In(['meat', 'fish', 'vegetarian'])
                ],
                'season' => [
                    'required',
                    Rule::In(['all']),
                ],
                'base' => [
                    'required',
                    Rule::In(['noodles', 'pasta', 'beans/lentils']),
                ],
                'protein_source' => [
                    'required',
                    Rule::In(['beef', 'seafood', 'pork', 'cheese', 'chicken', 'eggs', 'fish']),
                ],
                'preparation_time_minutes' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:255',
                ],
                'shelf_life_days' => [
                    'required',
                    'integer',
                    'min:0',
                    'max:255',
                ],
                'equipment_needed' => [
                    'required',
                    Rule::In(['None', 'Appetite', 'Pestle & Mortar (optional)']),
                ],
                'origin_country_id' => [
                    'required',
                    'integer',
                    'min:1',
                ],
                'recipe_cuisine' => [
                    'required',
                    Rule::In(['asian', 'italian', 'british', 'mediterranean', 'mexican']),
                ],
                'gousto_reference' => [
                    'required',
                    'integer',
                    'min:0',
                ],
            ]
        );
    }

    /**
     * @param mixed $data
     * @param int $code
     * @return mixed
     */
    protected function getResponse($data, $code = Response::HTTP_OK)
    {
        return response()->json($data, $code);
    }

    public function getAll(Request $Request)
    {
        $page = (int)$Request->query('page', 1);
        $cuisine = preg_replace('/\W/', '', (string)$Request->query('cuisine', ''));

        $cacheKey = $this->getCacheKey("all:c{$cuisine}:p{$page}");
        if ($cache = Cache::tags(self::CACHE_TAG_RECIPES)->get($cacheKey)) {
            $rows = $cache;
        } else {
            if ($cuisine = (string)$Request->query('cuisine', '')) {
                $rows = Recipe::where('recipe_cuisine', $cuisine)->orderBy('id', 'asc')->simplePaginate();
            } else {
                $rows = Recipe::orderBy('id', 'asc')->simplePaginate();
            }
            if ($rows) {
                Cache::tags(self::CACHE_TAG_RECIPES)->put($cacheKey, $rows->toArray(), self::CACHE_PUT_MIN);
            }
        }

        return $this->getResponse($rows);
    }

    public function getById(Request $Request, int $id)
    {
        $cacheKey = $this->getCacheKey($id);

        if ($cache = Cache::get($cacheKey)) {
            return $this->getResponse($cache, Response::HTTP_OK);
        }

        if (!$Recipe = Recipe::find($id)) {
            return $this->getResponse(null, Response::HTTP_NOT_FOUND);
        }

        $data = $Recipe->toArray();
        Cache::tags(self::CACHE_TAG_RECIPES)->flush();
        Cache::put($cacheKey, $data, self::CACHE_PUT_MIN);

        return $this->getResponse($data, Response::HTTP_OK);
    }

    public function create(Request $Request)
    {
        $Validator = $this->getValidator($Request);
        if (!$Validator->passes()) {
            return $this->getResponse($Validator->errors(), Response::HTTP_BAD_REQUEST);
        }
        $Recipe = new Recipe();
        $Recipe->fill($Request->input());
        if (!$Recipe->save()) {
            return $this->getResponse($Recipe, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $data = $Recipe->toArray();
        Cache::tags(self::CACHE_TAG_RECIPES)->flush();
        Cache::put($this->getCacheKey($Recipe->id), $data, self::CACHE_PUT_MIN);
        return $this->getResponse($data, Response::HTTP_CREATED);
    }

    public function update(Request $Request, int $id)
    {
        $Validator = $this->getValidator($Request);
        if (!$Validator->passes()) {
            return $this->getResponse($Validator->errors(), Response::HTTP_BAD_REQUEST);
        }
        $Recipe = Recipe::find($id);
        if (!$Recipe) {
            return $this->getResponse(null, Response::HTTP_NOT_FOUND);
        }
        $Recipe->fill($Request->input());
        if (!$Recipe->save()) {
            return $this->getResponse($Recipe, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $data = $Recipe->toArray();
        Cache::tags(self::CACHE_TAG_RECIPES)->flush();
        Cache::put($this->getCacheKey($Recipe->id), $data, self::CACHE_PUT_MIN);
        return $this->getResponse($data, Response::HTTP_OK);
    }

    public function rate(Request $Request, int $id)
    {
        $Validator = ValidatorFacade::make(
            $Request->input(),
            [
                'rate' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:5',
                ],
            ]
        );

        if (!$Validator->passes()) {
            return $this->getResponse($Validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $Recipe = Recipe::find($id);
        if (!$Recipe) {
            return $this->getResponse(null, Response::HTTP_NOT_FOUND);
        }

        $Rate = new Rate();
        $Rate->recipe_id = $id;
        $Rate->user_id = 42; // NOTE: In real application userId must be taken from session data
        $Rate->rate = (int)$Request->input('rate');

        if (!$Rate->save()) {
            return $this->getResponse($Recipe, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->getResponse($Rate, Response::HTTP_OK);
    }
}
