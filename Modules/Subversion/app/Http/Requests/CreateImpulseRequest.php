<?php

declare(strict_types=1);

namespace Modules\Subversion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Modules\Subversion\Models\Impulse;

use function collect;

class CreateImpulseRequest extends FormRequest
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
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
