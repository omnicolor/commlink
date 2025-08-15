<?php

declare(strict_types=1);

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Route;
use Modules\Shadowrun6e\Models\ActiveSkill;
use Modules\Shadowrun6e\Models\ComplexForm;
use Modules\Shadowrun6e\Transformers\ComplexFormResource;
use Modules\Shadowrun6e\Transformers\SkillResource;

Route::middleware(['auth:sanctum'])
    ->prefix('shadowrun6e')
    ->name('shadowrun6e.')
    ->group(static function (): void {
        Route::get(
            'complex-forms',
            static function (): AnonymousResourceCollection {
                return ComplexFormResource::collection(ComplexForm::all())
                    ->additional(['links' => [
                        'self' => route('shadowrun6e.complex-forms.index'),
                    ]]);
            }
        )->name('complex-forms.index');
        Route::get(
            'complex-forms/{complex_form}',
            static function (ComplexForm $complexForm): ComplexFormResource {
                return new ComplexFormResource($complexForm);
            },
        )->name('complex-forms.show');

        Route::get(
            'skills',
            static function (): AnonymousResourceCollection {
                return SkillResource::collection(ActiveSkill::all())
                    ->additional(['links' => [
                        'self' => route('shadowrun6e.skills.index'),
                    ]]);
            },
        )->name('skills.index');
        Route::get(
            'skills/{skill}',
            static function (ActiveSkill $skill): SkillResource {
                return new SkillResource($skill);
            },
        )->name('skills.show');
    });
