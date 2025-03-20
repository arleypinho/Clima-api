<?php

/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Rota para obter a frase sobre o dia atual
$router->get('/api/dia-atual', 'WeatherController@getDiaAtual');

// Rota para obter o clima detalhado
$router->get('/api/clima-detalhado', 'WeatherController@getClimaDetalhado');

// Rota para obter a previsão para os próximos 7 dias
$router->get('/api/previsao-7-dias', 'WeatherController@getPrevisao7Dias');

// Rota para obter a temperatura média de ontem
$router->get('/api/temperatura-ontem', 'WeatherController@getTemperaturaMediaOntem');

// Rota para converter temperatura
$router->get('/api/converter-temperatura', 'WeatherController@converterTemperatura');

// Rota para obter o horário do nascer e pôr do sol
$router->get('/api/nascer-por-do-sol', 'WeatherController@getNascerPorDoSol');

// Rota para obter a previsão de chuva
$router->get('/api/previsao-de-chuva', 'WeatherController@getPrevisaoDeChuva');

// Rota para comparar temperatura de hoje com a de ontem
$router->get('/api/comparar-temperatura', 'WeatherController@compararTemperatura');
