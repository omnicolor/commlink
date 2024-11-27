<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTokenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function authorize(): bool
    {
        return null !== $this->user() && $this->user()->is($this->user);
    }

    /**
     * Get the validation rules that apply to the request.
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'expires_at' => [
                'after:today',
                'date',
                'nullable',
            ],
            'name' => [
                'required',
                'string',
            ],
        ];
    }
}
