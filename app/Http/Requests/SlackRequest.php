<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Exceptions\SlackException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class SlackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a request failing validation.
     * @param Validator $validator
     * @throws SlackException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new SlackException();
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'channel_id' => [
                'filled',
                'required',
            ],
            'team_id' => [
                'filled',
                'required',
            ],
            'text' => [
                'filled',
                'required',
            ],
            'user_id' => [
                'filled',
                'required',
            ],
        ];
    }
}
