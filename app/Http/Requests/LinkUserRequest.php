<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request from a user to link a chat server to their Commlink user.
 */
class LinkUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'server-id' => [
                'alpha_num',
                'required',
            ],
            'server-type' => [
                'in:discord,slack',
                'required',
            ],
            'user-id' => [
                'required',
            ],
        ];
    }
}
