<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @psalm-suppress UnusedClass
 */
class CampaignInvitationResponseRequest extends FormRequest
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function authorize(): bool
    {
        /** @var Campaign */
        $campaign = $this->route('campaign');
        /** @var User */
        $player = $campaign->users->find($this->user()?->id);
        return null !== $player && 'invited' === $player->pivot->status;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'response' => [
                'in:accepted,removed',
                'required',
            ],
        ];
    }
}
