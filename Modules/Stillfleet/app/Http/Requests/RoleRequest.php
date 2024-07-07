<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Stillfleet\Models\Role;

class RoleRequest extends FormRequest
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<string, array<int, In|string>>
     */
    public function rules(): array
    {
        $roles = collect(Role::all())->keyBy(function (Role $role): string {
            return $role->id;
        })->keys();
        return [
            'role' => [
                'required',
                Rule::in($roles),
            ],
        ];
    }
}
