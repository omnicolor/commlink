<?php

declare(strict_types=1);

namespace App\Http\Requests\Transformers;

use Illuminate\Foundation\Http\FormRequest;

class StatisticsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'courage_robot' => [
                'integer',
                'min:1',
                'max:10',
                'required',
            ],
            'endurance_robot' => [
                'integer',
                'min:1',
                'max:10',
                'required',
            ],
            'firepower_robot' => [
                'integer',
                'min:1',
                'max:10',
                'required',
            ],
            'intelligence_robot' => [
                'integer',
                'min:1',
                'max:10',
                'required',
            ],
            'rank_robot' => [
                'integer',
                'min:1',
                'max:10',
                'required',
            ],
            'skill_robot' => [
                'integer',
                'min:1',
                'max:10',
                'required',
            ],
            'speed_robot' => [
                'integer',
                'min:1',
                'max:10',
                'required',
            ],
            'strength_robot' => [
                'integer',
                'min:1',
                'max:10',
                'required',
            ],
        ];
    }
}
