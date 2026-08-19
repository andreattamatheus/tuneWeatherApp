<?php

namespace App\Services;

use App\Exceptions\LocationForecastException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class CountryApiService
{
    protected string $apiKey;

    protected string $baseUri;

    protected array $params;

    public function __construct()
    {
        $this->baseUri = 'https://restcountries.com/v3.1/all';
    }

    public function sendRequest(): array
    {
        try {
            $response = Http::acceptJson()
                ->get($this->baseUri);

            return $this->decodeResponse($response, $this->baseUri);
        } catch (RequestException|\JsonException $e) {
            logger()->channel('weather-api')->error('[Open Weather API - Request] - '.$this->baseUri, [
                'PID' => getmypid(),
                'message' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => 'Error occurred while fetching the weather forecast.'];
        }
    }

    /**
     * @throws \JsonException
     * @throws LocationForecastException
     */
    public function decodeResponse(Response $response, string $endpoint = ''): array
    {
        $decodedResponse = json_decode($response->body(), true, 512, JSON_THROW_ON_ERROR);
        if ($response->failed()) {

            return ['success' => false, 'message' => 'An error countries'];
        }
        logger()->channel()->info('[Countries API - Response] - '.$endpoint, [
            'PID' => getmypid(),
            'response' => $decodedResponse,
        ]);

        return ['success' => true, 'data' => $decodedResponse];
    }

    /**
     * Get countries.
     */
    public function getCountries()
    {
        $path = storage_path('app/public/countries.json');
        if (File::exists($path) && File::size($path) > 0) {
            return json_decode(File::get($path), true);
        }

        return $this->storeCountries($path);
    }

    public function storeCountries(string $path)
    {
        $response = $this->sendRequest();

        foreach ($response['data'] as $country) {
            $countryData[] = [
                'name' => $country['name']['common'],
                'code' => $country['cca2'],
            ];
        }
        $countryData = array_map(function ($country) {
            return [
                'name' => $country['name']['common'],
                'code' => $country['cca2'],
            ];
        }, $response['data']);

        usort($countryData, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        File::put($path, json_encode($countryData, JSON_PRETTY_PRINT));

        return $countryData;
    }
}
