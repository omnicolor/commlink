<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Channel;
use Illuminate\Foundation\Http\FormRequest;

class ChannelDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $channel = Channel::find($this->route('channel'))?->first();
        return (bool)$this->user()?->is($channel?->owner);
    }
}
