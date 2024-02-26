<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Exceptions\SlackException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class SlackRequest extends FormRequest
{
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
                'required_without:payload',
                'string',
            ],
            'payload' => [
                'required_without_all:channel_id,team_id,text,user_id',
                'string',
            ],
            'team_id' => [
                'filled',
                'required_without:payload',
                'string',
            ],
            'text' => [
                'filled',
                'required_without:payload',
                'string',
            ],
            'user_id' => [
                'filled',
                'required_without:payload',
                'string',
            ],
        ];
    }
}
