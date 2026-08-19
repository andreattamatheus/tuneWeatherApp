<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocationStoreRequest;
use App\Http\Resources\ForecastResource;
use App\Http\Resources\LocationResource;
use App\Jobs\CreateLocationForecast;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Retrieve the locations associated with the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request  The incoming HTTP request.
     */
    public function get(Request $request, UserService $userService): mixed
    {
        try {
            $userLocations = $userService->getUserLocations($request);

            return LocationResource::collection($userLocations);
        } catch (\Exception $e) {
            logger()->channel('daily')->error('Error UserController getUserLocations: '.$e->getMessage());

            return response()->json([
                'message' => 'Error fetching user locations',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(LocationStoreRequest $request): ?JsonResponse
    {
        try {
            $weatherData = ForecastResource::make($request->validated())->resolve();
            CreateLocationForecast::dispatch($weatherData, $request->user());

            return response()->json([
                'message' => 'Location register successfully!',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            logger()->channel('daily')->error('Error saving user locations: '.$e->getMessage());

            return response()->json([
                'message' => 'Error saving user location',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a weather location.
     */
    public function destroy(Request $request, string $locationId, string $date, UserService $userService): ?JsonResponse
    {
        try {
            $user = $request->user();
            $userService->deleteLocation($locationId, $date, $user);

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
