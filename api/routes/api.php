<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

$g = function ($prefix, $routes, ...$args) {
    Route::group(array_merge(compact('prefix'), $args), $routes);
};

Route::get('/', function () {
    dd((new \App\Jobs\UpdatePlayerBlitz(\App\Models\User::find(65)))->handle());
});

$g('resource', function (Router $r) {
    $r->get('/', 'ResourceController@index');
});

$g('player', function (Router $r) {
    $r->get('short', 'PlayerController@short');
    $r->get('profile', 'PlayerController@show');
    $r->get('search', 'PlayerController@search');

    $r->post('update', 'PlayerController@update');
});
