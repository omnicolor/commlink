<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Event;
use App\Policies\EventPolicy;
use Illuminate\Foundation\Http\FormRequest;

class RsvpPutRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @phpstan-ignore-next-line */
        $event = Event::find($this->route('event'))->firstOrFail();
        $user = $this->user();
        return (new EventPolicy())->view($user, $event);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'response.in' => 'Response must be: accepted, declined, or tentative',
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'response' => [
                'in:accepted,declined,tentative',
                'required',
            ],
        ];
    }
}
