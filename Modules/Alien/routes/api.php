<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Alien\Http\Resources\CareerResource;
use Modules\Alien\Http\Resources\SkillResource;
use Modules\Alien\Models\Career;
use Modules\Alien\Models\Skill;

Route::middleware('auth:sanctum')
    ->prefix('alien')
    ->name('alien.')
    ->group(function (): void {
        Route::get('careers', function () {
            return CareerResource::collection(Career::all())
                ->additional(['self' => route('alien.careers.index')]);
        })->name('careers.index');
        Route::get('careers/{career}', function (string $career) {
            return new CareerResource(new Career($career));
        })->name('careers.show');

        Route::get('skills', function () {
            return SkillResource::collection(Skill::all())
                ->additional(['self' => route('alien.skills.index')]);
        })->name('skills.index');
        Route::get('skills/{skill}', function (string $skill) {
            return new SkillResource(new Skill($skill));
        })->name('skills.show');
    });
