<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Shadowrun5e\Models\Character;

class ContactCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @psalm-suppress PossiblyUnusedMethod
     * @return bool
     */
    public function authorize(): bool
    {
        /** @var Character */
        $character = $this->route('character');

        /** @var User */
        $user = $this->user();

        $campaign = $character->campaign();

        // Only GMs can create a contact. Without a campaign, there's no GM.
        if (null === $campaign) {
            return false;
        }

        if ($user->isNot($campaign->gamemaster)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'archetype' => [
                'required',
                'string',
            ],
            'connection' => [
                'integer',
                'max:12',
                'min:1',
                'nullable',
            ],
            'loyalty' => [
                'integer',
                'max:6',
                'min:1',
                'nullable',
            ],
            'gmNotes' => [
                'string',
                'nullable',
            ],
            'name' => [
                'required',
                'string',
            ],
            'notes' => [
                'string',
                'nullable',
            ],
        ];
    }
}
