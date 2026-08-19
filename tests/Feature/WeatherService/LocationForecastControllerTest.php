<?php

use App\DataTransferObjects\WeatherApi\WeatherApiResponseData;
use App\Exceptions\LocationForecastException;
use App\Models\User;
use App\Services\WeatherApiService;

beforeEach(function () {
    $this->user = User::factory()->make();
    $this->weatherApiService = new WeatherApiService;
});

test(/**
 * @throws JsonException
 */ 'fetch weather forecast successfully', function () {
    $responseFromApiSample = json_decode(file_get_contents(__DIR__.'/../../Stubs/WeatherApiResponse.json'), true, 512, JSON_THROW_ON_ERROR);

    $dataForDTO = [
        'list' => $responseFromApiSample['response']['list'],
        'city' => $responseFromApiSample['response']['city'],
    ];

    $this->mock(WeatherApiService::class)
        ->shouldReceive('getWeatherForecast')
        ->with('London', 'UK')
        ->once()
        ->andReturn(
            WeatherApiResponseData::from($dataForDTO)
        );

    $response = $this->actingAs($this->user, 'sanctum')->call('GET', '/api/v1/get-location-forecast', [
        'city' => 'London',
        'state' => 'UK',
    ]);

    $response->assertStatus(200);
});

test('fetch weather forecast has correct return format', function () {
    $responseFromApiSample = json_decode(
        file_get_contents(__DIR__.'/../../Stubs/WeatherApiResponse.json'),
        true
    );

    $dataForDTO = [
        'list' => $responseFromApiSample['response']['list'],
        'city' => $responseFromApiSample['response']['city'],
    ];

    $this->mock(WeatherApiService::class)
        ->shouldReceive('getWeatherForecast')
        ->with('London', 'UK')
        ->once()
        ->andReturn(
            WeatherApiResponseData::from($dataForDTO)
        );

    $response = $this->actingAs($this->user, 'sanctum')->call('GET', '/api/v1/get-location-forecast', [
        'city' => 'London',
        'state' => 'UK',
    ]);

    $response->assertJsonStructure([
        'data' => [
            'date',
            'min_temperature',
            'max_temperature',
            'condition',
            'icon',
            'city',
            'state',
        ],
    ]);
});

test('fetch weather forecast has incorrect return format', function () {
    $this->mock(WeatherApiService::class)
        ->shouldReceive('getWeatherForecast')
        ->with('London', 'UK')
        ->once()
        ->andThrow(new LocationForecastException('Failed to retrieve weather forecast data'));

    $response = $this->actingAs($this->user, 'sanctum')
        ->call('GET', '/api/v1/get-location-forecast', [
            'city' => 'London',
            'state' => 'UK',
        ]);

    $response->assertStatus(500);
});
