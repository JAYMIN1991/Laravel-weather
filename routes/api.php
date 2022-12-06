<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/weather', function () {
    $apiKey = config('services.openweather.key');
    $location = request('location');

    $response = Http::get("https://api.openweathermap.org/data/2.5/weather?q=$location&appid=$apiKey");

    return $response->json();
})->name('weather.index');