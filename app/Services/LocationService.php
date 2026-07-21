<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Country;
use App\Models\State;
use App\Models\City;

class LocationService
{
    /**
     * Get all active countries (paginated).
     */
    public function getCountries(): array
    {
        $countries = Country::where('is_active', true)->paginate(1);

        return [
            'status'  => true,
            'message' => __('messages.countries_retrieved_successfully'),
            'data'    => $countries,
        ];
    }

    /**
     * Get all active states for a country (paginated).
     */
    public function getStatesByCountry(int $countryId): array
    {
        $country = Country::find($countryId);
        if (!$country) {
            return [
                'status'  => false,
                'message' => __('messages.country_not_found'),
            ];
        }

        $states = State::where('country_id', $countryId)->where('is_active', true)->paginate(15);

        return [
            'status'  => true,
            'message' => __('messages.states_retrieved_successfully'),
            'data'    => $states,
        ];
    }

    /**
     * Get all active cities for a state (paginated).
     */
    public function getCitiesByState(int $stateId): array
    {
        $state = State::find($stateId);
        if (!$state) {
            return [
                'status'  => false,
                'message' => __('messages.state_not_found'),
            ];
        }

        $cities = City::where('state_id', $stateId)->where('is_active', true)->paginate(10);

        return [
            'status'  => true,
            'message' => __('messages.cities_retrieved_successfully'),
            'data'    => $cities,
        ];
    }
    public function StoreAddresses(array $data) : array
    {
        $address = Address::create($data);
        return [
            'status'  => true,
            'message' => __('messages.address_added_successfully'),
            'data'    => $address,
        ];
    }
}
