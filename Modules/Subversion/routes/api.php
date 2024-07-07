<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Route;
use Modules\Subversion\Http\Resources\BackgroundResource;
use Modules\Subversion\Http\Resources\CasteResource;
use Modules\Subversion\Http\Resources\CharacterResource;
use Modules\Subversion\Http\Resources\GearResource;
use Modules\Subversion\Http\Resources\IdeologyResource;
use Modules\Subversion\Http\Resources\ImpulseResource;
use Modules\Subversion\Http\Resources\LanguageResource;
use Modules\Subversion\Http\Resources\LineageResource;
use Modules\Subversion\Http\Resources\OriginResource;
use Modules\Subversion\Http\Resources\SkillResource;
use Modules\Subversion\Models\Background;
use Modules\Subversion\Models\Caste;
use Modules\Subversion\Models\Character;
use Modules\Subversion\Models\Gear;
use Modules\Subversion\Models\Ideology;
use Modules\Subversion\Models\Impulse;
use Modules\Subversion\Models\Language;
use Modules\Subversion\Models\Lineage;
use Modules\Subversion\Models\Origin;
use Modules\Subversion\Models\Skill;

Route::middleware('auth:sanctum')
    ->prefix('subversion')
    ->name('subversion.')
    ->group(function (): void {
        Route::get('backgrounds', function (): AnonymousResourceCollection {
            return BackgroundResource::collection(Background::all());
        })->name('backgrounds.index');
        Route::get(
            'backgrounds/{background}',
            function (string $background): BackgroundResource {
                return new BackgroundResource(new Background($background));
            }
        )->name('backgrounds.show');

        Route::get('castes', function (): AnonymousResourceCollection {
            return CasteResource::collection(Caste::all());
        })->name('castes.index');
        Route::get(
            'castes/{caste}',
            function (string $caste): CasteResource {
                return new CasteResource(new Caste($caste));
            }
        )->name('castes.show');

        Route::get(
            'characters',
            function (Request $request): AnonymousResourceCollection {
                return CharacterResource::collection(
                    // @phpstan-ignore method.nonObject
                    $request->user()
                        ->characters('subversion')
                        ->get()
                )
                    ->additional([
                        'links' => [
                            'self' => route('subversion.characters.index'),
                        ],
                    ]);
            }
        )->name('characters.index');
        Route::get(
            'characters/{character}',
            function (Character $character): CharacterResource {
                return new CharacterResource($character);
            }
        )->name('characters.show');

        Route::get('gear', function (): AnonymousResourceCollection {
            return GearResource::collection(Gear::all());
        })->name('gear.index');
        Route::get(
            'gear/{gear}',
            function (string $gear): GearResource {
                return new GearResource(new Gear($gear));
            }
        )->name('gear.show');

        Route::get('ideologies', function (): AnonymousResourceCollection {
            return IdeologyResource::collection(Ideology::all());
        })->name('ideologies.index');
        Route::get(
            'ideologies/{ideology}',
            function (string $ideology): IdeologyResource {
                return new IdeologyResource(new Ideology($ideology));
            }
        )->name('ideologies.show');

        Route::get('impulses', function (): AnonymousResourceCollection {
            return ImpulseResource::collection(Impulse::all());
        })->name('impulses.index');
        Route::get(
            'impulses/{impulse}',
            function (string $impulse): ImpulseResource {
                return new ImpulseResource(new Impulse($impulse));
            }
        )->name('impulses.show');

        Route::get('languages', function (): AnonymousResourceCollection {
            return LanguageResource::collection(Language::all());
        })->name('languages.index');
        Route::get(
            'languages/{language}',
            function (string $language): LanguageResource {
                return new LanguageResource(new Language($language));
            }
        )->name('languages.show');

        Route::get('lineages', function (): AnonymousResourceCollection {
            return LineageResource::collection(Lineage::all());
        })->name('lineages.index');
        Route::get(
            'lineages/{lineage}',
            function (string $lineage): LineageResource {
                return new LineageResource(new Lineage($lineage));
            }
        )->name('lineages.show');

        Route::get('origins', function (): AnonymousResourceCollection {
            return OriginResource::collection(Origin::all());
        })->name('origins.index');
        Route::get('origins/{origin}', function (string $origin): OriginResource {
            return new OriginResource(new Origin($origin));
        })->name('origins.show');

        Route::get('skills', function (): AnonymousResourceCollection {
            return SkillResource::collection(Skill::all());
        })->name('skills.index');
        Route::get(
            'skills/{skill}',
            function (string $skill): SkillResource {
                return new SkillResource(new Skill($skill));
            }
        )->name('skills.show');
    });
