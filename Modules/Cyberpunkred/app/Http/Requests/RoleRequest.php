<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Cyberpunkred\Models\Role;

use function array_walk;

class RoleRequest extends FormRequest
{
    /**
     * @return array<string, array<int, In|string>>
     */
    public function rules(): array
    {
        $roles = Role::all()->getArrayCopy();
        array_walk($roles, function (&$value): void {
            $value = (string)$value;
        });
        return [
            'role' => [
                'filled',
                'required',
                Rule::in($roles),
            ],
        ];
    }
}
