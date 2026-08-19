<?php

use App\Services\WeatherApiService;
use Illuminate\Support\Facades\Http;

uses(Tests\TestCase::class);

test('weather forecast requests the configured API and maps the first forecast', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'list' => [
                [
                    'dt_txt' => '2024-10-31 03:00:00',
                    'main' => [
                        'temp_min' => 9.22,
                        'temp_max' => 10.71,
                    ],
                    'weather' => [[
                        'description' => 'broken clouds',
                        'icon' => '04n',
                    ]],
                ],
            ],
            'city' => [
                'name' => 'London',
                'country' => 'GB',
            ],
        ]),
    ]);

    $forecast = (new WeatherApiService)->getWeatherForecast('London', 'UK');

    Http::assertSent(function ($request) {
        return str_ends_with(parse_url($request->url(), PHP_URL_PATH), '/forecast')
            && $request->data()['q'] === 'London,UK'
            && $request->data()['units'] === 'metric'
            && $request->data()['APPID'] === config('services.openWeather.key');
    });

    expect($forecast->city->name)->toBe('London')
        ->and($forecast->list[0]->main->temp_min)->toBe(9.22)
        ->and($forecast->list[0]->weather[0]->description)->toBe('broken clouds');
});

test('weather API request returns a failure payload when the provider is unavailable', function () {
    Http::fake([
        '*' => Http::response(['message' => 'Service unavailable'], 503),
    ]);

    $result = (new WeatherApiService)->sendRequest('forecast');

    expect($result)->toBe([
        'success' => false,
        'message' => 'An error occurred while fetching the weather forecast.',
    ]);
});
