<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Alien\Http\Controllers\CharactersController;
use Modules\Alien\Http\Resources\ArmorResource;
use Modules\Alien\Http\Resources\CareerResource;
use Modules\Alien\Http\Resources\GearResource;
use Modules\Alien\Http\Resources\InjuryResource;
use Modules\Alien\Http\Resources\SkillResource;
use Modules\Alien\Http\Resources\TalentResource;
use Modules\Alien\Http\Resources\WeaponResource;
use Modules\Alien\Models\Armor;
use Modules\Alien\Models\Career;
use Modules\Alien\Models\Gear;
use Modules\Alien\Models\Injury;
use Modules\Alien\Models\Skill;
use Modules\Alien\Models\Talent;
use Modules\Alien\Models\Weapon;

Route::middleware('auth:sanctum')
    ->prefix('alien')
    ->name('alien.')
    ->group(function (): void {
        Route::get('armor', function () {
            return ArmorResource::collection(Armor::all())
                ->additional(['self' => route('alien.armor.index')]);
        })->name('armor.index');
        Route::get('armor/{armor}', function (string $armor) {
            return new ArmorResource(new Armor($armor));
        })->name('armor.show');

        Route::get('careers', function () {
            return CareerResource::collection(Career::all())
                ->additional(['self' => route('alien.careers.index')]);
        })->name('careers.index');
        Route::get('careers/{career}', function (string $career) {
            return new CareerResource(new Career($career));
        })->name('careers.show');

        Route::resource('characters', CharactersController::class)
            ->only(['index', 'show']);

        Route::get('gear', function () {
            return GearResource::collection(Gear::all())
                ->additional(['self' => route('alien.gear.index')]);
        })->name('gear.index');
        Route::get('gear/{gear}', function (string $gear) {
            return new GearResource(new Gear($gear));
        })->name('gear.show');

        Route::get('injuries', function () {
            return InjuryResource::collection(Injury::all())
                ->additional(['self' => route('alien.injuries.index')]);
        })->name('injuries.index');
        Route::get('injuries/{injury}', function (string $injury) {
            return new InjuryResource(new Injury($injury));
        })->name('injuries.show');

        Route::get('skills', function () {
            return SkillResource::collection(Skill::all())
                ->additional(['self' => route('alien.skills.index')]);
        })->name('skills.index');
        Route::get('skills/{skill}', function (string $skill) {
            return new SkillResource(new Skill($skill));
        })->name('skills.show');

        Route::get('talents', function () {
            return TalentResource::collection(Talent::all())
                ->additional(['self' => route('alien.talents.index')]);
        })->name('talents.index');
        Route::get('talents/{talent}', function (string $talent) {
            return new TalentResource(new Talent($talent));
        })->name('talents.show');

        Route::get('weapons', function () {
            return WeaponResource::collection(Weapon::all())
                ->additional(['self' => route('alien.weapons.index')]);
        })->name('weapons.index');
        Route::get('weapons/{weapon}', function (string $weapon) {
            return new WeaponResource(new Weapon($weapon));
        })->name('weapons.show');
    });
