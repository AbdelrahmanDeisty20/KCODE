<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class GeneralNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('device_id') && $this->hasHeader('X-Device-ID')) {
            $this->merge([
                'device_id' => $this->header('X-Device-ID'),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'device_id' => 'required|string',
        ];
    }
}
