<?php

//Liste quem pode ter acesso as requisições
header('Access-Control-Allow-Origin: *');

//Defina os verbos aceitos
header('Access-Control-Allow-Methods: GET,HEAD,PUT,POST,DELETE,PATCH,OPTIONS');

//Defina os cabeçalhos aceitos
header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin, Authorization');

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    return $router->app->version();
});

//Create new user

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('/user', 'UserController@create');
});

$router->group(['prefix' => 'api', 'middleware' => 'auth'], function () use ($router) {
    $router->get('health', function () {
        return response()->json('OK', 200);
    });

    // Rota para obter a lista de todos os pokemons
    $router->get('/pokemons', 'PokemonController@index');

    // Rota para exibir detalhes de um pokémon específico
    $router->get('/pokemons/{id}', 'PokemonController@show');

    // Rota para cadastrar um novo pokémon
    $router->post('/pokemons', 'PokemonController@store');

    // Rota para atualizar um pokémon existente
    $router->put('/pokemons/{id}', 'PokemonController@update');

    // Rota para excluir um pokémon
    $router->delete('/pokemons/{id}', 'PokemonController@destroy');
});


$router->options(
    '/{any:.*}',
    [
        function () {
            return response(['status' => 'success']);
        }
    ]
);
