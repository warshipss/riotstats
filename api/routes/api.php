<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

$g = function ($prefix, $routes, ...$args) {
    Route::group(array_merge(compact('prefix'), $args), $routes);
};

$g('player', function (Router $r) {
    $r->get('profile', 'PlayerController@show');
});
