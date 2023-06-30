<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseRequest;

/**
 * @psalm-suppress UnusedClass
 */
abstract class FormRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, string>
     */
    abstract public function rules(): array;
}
