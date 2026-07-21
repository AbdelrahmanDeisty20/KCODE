<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ADDRESS\AddressRequest;
use App\Http\Requests\WEB\AddressupdateRequest;
use App\Http\Resources\API\ADDRESS\AddressResource;
use App\Services\AddressService;
use App\Traits\ApiResponse;

class AddressController extends Controller
{
    use ApiResponse;

    public function __construct(private AddressService $addressService) {}

    /**
     * Get user addresses.
     */
    public function index()
    {
        $userId = auth()->id();
        $result = $this->addressService->getAddresses($userId);

        return $this->paginated(
            AddressResource::class,
            $result['data'],
            $result['message']
        );
    }

    /**
     * Store new user address.
     */
    public function store(AddressRequest $request)
    {
        $userId = auth()->id();
        $result = $this->addressService->storeAddress($userId, $request->validated());

        return $this->success(
            new AddressResource($result['data']),
            $result['message']
        );
    }

    /**
     * Update user address.
     */
    public function update(AddressupdateRequest $request, int $id)
    {
        $userId = auth()->id();
        $result = $this->addressService->updateAddress($userId, $id, $request->validated());

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(
            new AddressResource($result['data']),
            $result['message']
        );
    }

    /**
     * Delete user address.
     */
    public function destroy(int $id)
    {
        $userId = auth()->id();
        $result = $this->addressService->deleteAddress($userId, $id);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(
            [],
            $result['message']
        );
    }

    /**
     * Set address as default.
     */
    public function setDefault(int $id)
    {
        $userId = auth()->id();
        $result = $this->addressService->setDefaultAddress($userId, $id);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(
            new AddressResource($result['data']),
            $result['message']
        );
    }
}
