<?php

namespace App\Services;

use App\Models\Address;

class AddressService
{
    /**
     * Get all addresses for a user.
     */
    public function getAddresses(int $userId): array
    {
        $addresses = Address::where('user_id', $userId)
            ->with(['country', 'state', 'city'])
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return [
            'status'  => true,
            'message' => __('messages.addresses_retrieved_successfully'),
            'data'    => $addresses,
        ];
    }

    /**
     * Store a new address.
     */
    public function storeAddress(int $userId, array $data): array
    {
        // If this is the first address, make it default regardless of is_default input
        $hasAddress = Address::where('user_id', $userId)->exists();
        if (!$hasAddress) {
            $data['is_default'] = true;
        }

        // If is_default is true, unset default status on all other user's addresses
        if (!empty($data['is_default'])) {
            Address::where('user_id', $userId)->update(['is_default' => false]);
        }

        $data['user_id'] = $userId;
        $address = Address::create($data);

        return [
            'status'  => true,
            'message' => __('messages.address_created_successfully'),
            'data'    => $address->load(['country', 'state', 'city']),
        ];
    }

    /**
     * Update an address.
     */
    public function updateAddress(int $userId, int $id, array $data): array
    {
        $address = Address::where('user_id', $userId)->find($id);

        if (!$address) {
            return [
                'status'  => false,
                'message' => __('messages.address_not_found'),
            ];
        }

        // If is_default is updated to true, unset default status on all other user's addresses
        if (!empty($data['is_default'])) {
            Address::where('user_id', $userId)->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $address->update($data);

        return [
            'status'  => true,
            'message' => __('messages.address_updated_successfully'),
            'data'    => $address->load(['country', 'state', 'city']),
        ];
    }

    /**
     * Delete an address.
     */
    public function deleteAddress(int $userId, int $id): array
    {
        $address = Address::where('user_id', $userId)->find($id);

        if (!$address) {
            return [
                'status'  => false,
                'message' => __('messages.address_not_found'),
            ];
        }

        $wasDefault = $address->is_default;
        $address->delete();

        // If the deleted address was default, make the next latest address default
        if ($wasDefault) {
            $nextDefault = Address::where('user_id', $userId)->latest()->first();
            if ($nextDefault) {
                $nextDefault->update(['is_default' => true]);
            }
        }

        return [
            'status'  => true,
            'message' => __('messages.address_deleted_successfully'),
            'data'=>[]
        ];
    }

    /**
     * Set default address.
     */
    public function setDefaultAddress(int $userId, int $id): array
    {
        $address = Address::where('user_id', $userId)->find($id);

        if (!$address) {
            return [
                'status'  => false,
                'message' => __('messages.address_not_found'),
            ];
        }

        Address::where('user_id', $userId)->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return [
            'status'  => true,
            'message' => __('messages.default_address_set_successfully'),
            'data'    => $address->load(['country', 'state', 'city']),
        ];
    }
}
