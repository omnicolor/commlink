<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Campaign;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @psalm-suppress UnusedClass
 */
class CampaignInvitationResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Campaign */
        $campaign = $this->route('campaign');
        $player = $campaign->users->find($this->user()?->id);
        return null !== $player && 'invited' === $player->pivot->status;
    }

    /**
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
