<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @psalm-suppress UnusedClass
 */
class EventPostRequest extends FormRequest
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if (null === $this->campaign?->gamemaster) {
            return false;
        }
        return $this->campaign->gamemaster->is($user);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'description' => [
                'nullable',
                'string',
            ],
            'game_end' => [
                'nullable',
                'string',
            ],
            'game_start' => [
                'nullable',
                'string',
            ],
            'name' => [
                'string',
            ],
            'real_end' => [
                'after:real_start',
                'date',
                'nullable',
            ],
            'real_start' => [
                'date',
                'required',
            ],
        ];
    }
}
