<?php

declare(strict_types=1);

namespace App\Http\Requests\Transformers;

use App\Models\Transformers\AltMode;
use Illuminate\Foundation\Http\FormRequest;

class AltModeRequest extends FormRequest
{
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
