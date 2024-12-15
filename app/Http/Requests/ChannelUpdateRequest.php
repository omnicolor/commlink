<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Channel;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property Channel $channel
 */
class ChannelUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return null !== $this->user()
            && (int)$this->channel->registered_by === $this->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, string|callable>>
     */
    public function rules(): array
    {
        return [
            'auto' => [
                'boolean',
                'required_without:webhook',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (Channel::TYPE_DISCORD !== $this->channel->type) {
                        $fail('Auto only works for Discord channels.');
                    }
                },
            ],
            'webhook' => [
                'required_without:auto',
                'url',
            ],
        ];
    }
}
