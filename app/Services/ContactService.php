<?php

namespace App\Services;

use App\Models\Contact;

class ContactService
{
    /**
     * Store a new contact message.
     */
    public function createContact(array $data, ?int $userId = null): array
    {
        if ($userId) {
            $data['user_id'] = $userId;
        }

        $contact = Contact::create($data);

        return [
            'status'  => true,
            'message' => __('messages.contact_sent_successfully'),
            'data'    => $contact,
        ];
    }

    /**
     * Get contact history for a specific user.
     */
    public function getUserContacts(?int $userId): array
    {
        if (!$userId) {
            return [
                'status'  => false,
                'message' => __('messages.unauthorized'),
                'data'    => [],
            ];
        }

        $contacts = Contact::where('user_id', $userId)
            ->latest()
            ->get();

        return [
            'status'  => true,
            'message' => __('messages.contacts_retrieved_successfully'),
            'data'    => $contacts,
        ];
    }
}
