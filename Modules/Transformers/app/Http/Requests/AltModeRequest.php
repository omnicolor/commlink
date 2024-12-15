<?php

declare(strict_types=1);

namespace Modules\Transformers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Not yet used.
 * @codeCoverageIgnore
 */
class AltModeRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'mode' => [
                'required',
                'string',
            ],
        ];
    }
}
