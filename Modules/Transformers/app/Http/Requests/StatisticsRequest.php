<?php

declare(strict_types=1);

namespace Modules\Transformers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StatisticsRequest extends FormRequest
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
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
