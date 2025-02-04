<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class DeleteChatUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = User::find($this->route('user'))?->first();
        $chat_user = ChatUser::find($this->route('chat_user'))?->first();
        return null !== $user
            && $user->is($this->user())
            && $user->can('delete', $chat_user);
    }
}
