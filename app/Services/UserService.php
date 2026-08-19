<?php

namespace App\Services;

use App\Exceptions\LocationForecastException;
use App\Http\Resources\ForecastResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function __construct(private LocationForecastService $locationForecastService) {}

    public function getUserLocations(Request $request): mixed
    {
        try {
            $user = $request->user()->query()->with('locations')->first();

            return $user->locations()->with(['forecasts' => function ($query) {
                $query->select('id', 'location_id', 'date', 'min_temperature', 'max_temperature', 'condition', 'icon');
            }])->paginate($request->get('per_page', 10));
        } catch (\Throwable $th) {
            logger()->channel('daily')->error('Error getUserLocations locations: '.$th->getMessage());

            throw new Exception('An error occurred while getting user locations.');
        }
    }

    /**
     * Store the location forecast data.
     */
    public function store(array $weatherData, User $user): void
    {
        $weatherData = ForecastResource::make($weatherData);
        $location = $this->locationForecastService->createLocation($weatherData, $user);
        $this->locationForecastService->createLocationForecast($location, $weatherData);
    }

    /**
     * Deletes a location forecast for a specific date and user.
     *
     * @param  string  $locationId  The ID of the location to be deleted.
     * @param  string  $date  The date for which the location forecast should be deleted.
     * @param  User  $user  The user requesting the deletion.
     */
    public function deleteLocation(string $locationId, string $date, User $user): void
    {
        try {
            $location = $user->locations()->findOrFail($locationId);
            $forecast = $location->forecasts()->where('date', $date)->first();

            DB::transaction(static function () use ($forecast) {
                $forecast->delete();
            });
        } catch (\Throwable $th) {
            logger()->channel('daily')->error('Error destroy user locations: '.$th->getMessage());

            throw new LocationForecastException('Error deleting location forecast data.');
        }
    }
}
