<?php

namespace App\Services;

use App\Http\Resources\API\LOCATION\CityResource;
use App\Http\Resources\API\LOCATION\CountryResource;
use App\Http\Resources\API\LOCATION\StateResource;
use App\Models\Address;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Support\StaticCache;

class LocationService
{
    /**
     * Get all active countries (cached).
     */
    public function getCountries(): array
    {
        $countries = StaticCache::remember('locations', 'countries', function () {
            return Country::where('is_active', true)->get();
        });

        return [
            'status'  => true,
            'message' => __('messages.countries_retrieved_successfully'),
            'data'    => CountryResource::collection($countries),
        ];
    }

    /**
     * Get all active states for a country (cached).
     */
    public function getStatesByCountry(int $countryId): array
    {
        $cached = StaticCache::remember('locations', "states_country_{$countryId}", function () use ($countryId) {
            $country = Country::find($countryId);
            if (!$country) {
                return ['exists' => false, 'states' => collect()];
            }

            return [
                'exists' => true,
                'states' => State::where('country_id', $countryId)->where('is_active', true)->get(),
            ];
        });

        if (!$cached['exists']) {
            return [
                'status'  => false,
                'message' => __('messages.country_not_found'),
            ];
        }

        return [
            'status'  => true,
            'message' => __('messages.states_retrieved_successfully'),
            'data'    => StateResource::collection($cached['states']),
        ];
    }

    /**
     * Get all active cities for a state (cached).
     */
    public function getCitiesByState(int $stateId): array
    {
        $cached = StaticCache::remember('locations', "cities_state_{$stateId}", function () use ($stateId) {
            $state = State::find($stateId);
            if (!$state) {
                return ['exists' => false, 'cities' => collect()];
            }

            return [
                'exists' => true,
                'cities' => City::where('state_id', $stateId)->where('is_active', true)->get(),
            ];
        });

        if (!$cached['exists']) {
            return [
                'status'  => false,
                'message' => __('messages.state_not_found'),
            ];
        }

        return [
            'status'  => true,
            'message' => __('messages.cities_retrieved_successfully'),
            'data'    => CityResource::collection($cached['cities']),
        ];
    }

    /**
     * Get all active cities with shipping fee (cached).
     */
    public function getShippingFees(): array
    {
        $cities = StaticCache::remember('locations', 'shipping_fees', function () {
            return City::where('is_active', true)->get();
        });

        return [
            'status'  => true,
            'message' => __('messages.cities_retrieved_successfully'),
            'data'    => \App\Http\Resources\API\LOCATION\ShippingResource::collection($cities),
        ];
    }
}
