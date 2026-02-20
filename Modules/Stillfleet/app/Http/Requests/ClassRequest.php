<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Stillfleet\Models\Role;

use function collect;

class ClassRequest extends FormRequest
{
    /**
     * @return array<string, array<int, In|string>>
     */
    public function rules(): array
    {
        $roles = collect(Role::all())->pluck('id');
        return [
            'role' => [
                'required',
                Rule::in($roles),
            ],
        ];
    }
}
