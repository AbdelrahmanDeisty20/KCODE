<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Contact\StoreContactRequest;
use App\Http\Resources\API\Contact\ContactResource;
use App\Services\ContactService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    use ApiResponse;

    public function __construct(private ContactService $contactService) {}

    /**
     * Store a newly created contact message in storage.
     */
    public function store(StoreContactRequest $request): JsonResponse
    {
        $userId = auth('sanctum')->check() ? auth('sanctum')->id() : null;

        $result = $this->contactService->createContact($request->validated(), $userId);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->created(
            new ContactResource($result['data']),
            $result['message']
        );
    }

    /**
     * Display a listing of user contact messages.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = auth('sanctum')->id();

        $result = $this->contactService->getUserContacts($userId);

        if (!$result['status']) {
            return $this->error($result['message'], 401);
        }

        return $this->success(
            ContactResource::collection($result['data']),
            $result['message']
        );
    }
}
