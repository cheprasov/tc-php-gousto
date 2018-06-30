<?php

namespace Tests\Unit;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Recipe\RecipesController;
use Tests\TestCase;

class ControllerTest extends TestCase
{
    public function providerGetRouteMethod()
    {
        return [
            'line_' . __LINE__ => [
                'class' => Controller::class,
                'method' => 'foo',
                'expect' => '\\' . Controller::class . '@foo',
            ],
            'line_' . __LINE__ => [
                'class' => Controller::class,
                'method' => 'bar',
                'expect' => '\\' . Controller::class . '@bar',
            ],
            'line_' . __LINE__ => [
                'class' => RecipesController::class,
                'method' => 'index',
                'expect' => '\\' . RecipesController::class . '@index',
            ],
            'line_' . __LINE__ => [
                'class' => RecipesController::class,
                'method' => 'getAll',
                'expect' => '\\' . RecipesController::class . '@getAll',
            ],
        ];
    }

    /**
     * @see \App\Http\Controllers\Controller::getRouteMethod
     * @dataProvider providerGetRouteMethod
     */
    public function testGetRouteMethod($class, $method, $expect)
    {
        $result = call_user_func([$class, 'getRouteMethod'], $method);
        $this->assertSame($expect, $result);
    }
}
