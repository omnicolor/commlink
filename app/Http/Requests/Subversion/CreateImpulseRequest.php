<?php

declare(strict_types=1);

namespace App\Http\Requests\Subversion;

use App\Models\Subversion\Impulse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

use function collect;

class CreateImpulseRequest extends FormRequest
{
    /**
     * @return array{
     *   impulse: array<int, In|string>
     * }
     */
    public function rules(): array
    {
        $impulses = collect(Impulse::all())->pluck('id');
        return [
            'impulse' => [
                'required',
                Rule::in($impulses),
            ],
        ];
    }
}
