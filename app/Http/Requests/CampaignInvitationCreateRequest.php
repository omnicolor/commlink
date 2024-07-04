<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Campaign;
use App\Models\User;
use App\Policies\CampaignPolicy;
use Illuminate\Foundation\Http\FormRequest;

class CampaignInvitationCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function authorize(): bool
    {
        /** @var Campaign */
        $campaign = $this->route('campaign');

        /** @var User */
        $user = $this->user();
        return (new CampaignPolicy())->invite($user, $campaign);
    }

    /**
     * Get the validation rules that apply to the request.
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'email',
                'required',
            ],
            'name' => [
                'required',
                'string',
            ],
        ];
    }
}
