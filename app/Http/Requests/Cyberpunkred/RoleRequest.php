<?php

declare(strict_types=1);

namespace App\Http\Requests\Cyberpunkred;

use App\Models\Cyberpunkred\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

class RoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
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
