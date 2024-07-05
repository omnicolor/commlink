<?php

declare(strict_types=1);

namespace App\Http\Requests\Subversion;

use Illuminate\Foundation\Http\FormRequest;

class CreateHooksRequest extends FormRequest
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array{
     *   hook1: array<int, string>,
     *   hook2: array<int, string>,
     * }
     */
    public function rules(): array
    {
        return [
            'hook1' => [
                'required',
                'string',
            ],
            'hook2' => [
                'required',
                'string',
            ],
        ];
    }
}
