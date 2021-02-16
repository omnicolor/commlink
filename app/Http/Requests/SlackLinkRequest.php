<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request from a user to link a Slack team and user to their account.
 */
class SlackLinkRequest extends FormRequest
{
    /**
     * Get the error messages for the defined validation rules.
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slack-team.required' => 'Enter your Slack Team ID',
            'slack-user.required' => 'Enter your Slack User ID',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'slack-team' => 'required|filled',
            'slack-user' => 'required|filled',
        ];
    }
}
