<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\LOCATION\CountryResource;
use App\Http\Resources\API\LOCATION\StateResource;
use App\Http\Resources\API\LOCATION\CityResource;
use App\Services\LocationService;
use App\Traits\ApiResponse;

class LocationController extends Controller
{
    use ApiResponse;

    public function __construct(private LocationService $locationService) {}

    /**
     * Get all active countries (paginated).
     */
    public function getCountries()
    {
        $result = $this->locationService->getCountries();
        return $this->paginated(
            CountryResource::class,
            $result['data'],
            $result['message']
        );
    }

    /**
     * Get all active states for a country (paginated).
     */
    public function getStates(int $countryId)
    {
        $result = $this->locationService->getStatesByCountry($countryId);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->paginated(
            StateResource::class,
            $result['data'],
            $result['message']
        );
    }

    /**
     * Get all active cities for a state (paginated).
     */
    public function getCities(int $stateId)
    {
        $result = $this->locationService->getCitiesByState($stateId);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->paginated(
            CityResource::class,
            $result['data'],
            $result['message']
        );
    }
}
