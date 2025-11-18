<?php

declare(strict_types=1);

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Route;
use Modules\Battletech\Http\Controllers\CharactersController;
use Modules\Battletech\Models\Quality;
use Modules\Battletech\Models\Skill;
use Modules\Battletech\Transformers\SkillResource;
use Modules\Battletech\Transformers\TraitResource;

Route::middleware(['auth:sanctum'])
    ->prefix('battletech')
    ->name('battletech.')
    ->group(function (): void {
        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show']);

        Route::get('skills', function (): AnonymousResourceCollection {
            return SkillResource::collection(Skill::all())
                ->additional(['links' => [
                    'self' => route('battletech.skills.index'),
                ]]);
        })->name('skills.index');
        Route::get('skills/{skill}', function (Skill $skill): SkillResource {
            return new SkillResource($skill);
        })->name('skills.show');

        Route::get('traits', function (): AnonymousResourceCollection {
            return TraitResource::collection(Quality::all())
                ->additional(['links' => [
                    'self' => route('battletech.traits.index'),
                ]]);
        })->name('traits.index');
        Route::get('traits/{trait}', function (Quality $trait): TraitResource {
            return new TraitResource($trait);
        })->name('traits.show');
    });
