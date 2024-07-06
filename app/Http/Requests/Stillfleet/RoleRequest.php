<?php

declare(strict_types=1);

namespace App\Http\Requests\Stillfleet;

use App\Models\Stillfleet\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

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
