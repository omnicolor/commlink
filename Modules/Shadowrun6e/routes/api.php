<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Route;
use Modules\Shadowrun6e\Models\ActiveSkill;
use Modules\Shadowrun6e\Models\AdeptPower;
use Modules\Shadowrun6e\Models\ComplexForm;
use Modules\Shadowrun6e\Models\MentorSpirit;
use Modules\Shadowrun6e\Models\Metamagic;
use Modules\Shadowrun6e\Models\Program;
use Modules\Shadowrun6e\Models\Quality;
use Modules\Shadowrun6e\Models\Race;
use Modules\Shadowrun6e\Models\ResonanceEcho;
use Modules\Shadowrun6e\Models\Ritual;
use Modules\Shadowrun6e\Models\Spell;
use Modules\Shadowrun6e\Models\Spirit;
use Modules\Shadowrun6e\Transformers\AdeptPowerResource;
use Modules\Shadowrun6e\Transformers\ComplexFormResource;
use Modules\Shadowrun6e\Transformers\EchoResource;
use Modules\Shadowrun6e\Transformers\MentorSpiritResource;
use Modules\Shadowrun6e\Transformers\MetamagicResource;
use Modules\Shadowrun6e\Transformers\ProgramResource;
use Modules\Shadowrun6e\Transformers\QualityResource;
use Modules\Shadowrun6e\Transformers\RaceResource;
use Modules\Shadowrun6e\Transformers\RitualResource;
use Modules\Shadowrun6e\Transformers\SkillResource;
use Modules\Shadowrun6e\Transformers\SpellResource;
use Modules\Shadowrun6e\Transformers\SpiritResource;

Route::middleware(['auth:sanctum'])
    ->prefix('shadowrun6e')
    ->name('shadowrun6e.')
    ->group(static function (): void {
        Route::get(
            'adept-powers',
            static function (): AnonymousResourceCollection {
                return AdeptPowerResource::collection(AdeptPower::all())
                    ->additional(['links' => [
                        'self' => route('shadowrun6e.adept-powers.index'),
                    ]]);
            },
        )->name('adept-powers.index');
        Route::get(
            'adept-powers/{adept_power}',
            static function (AdeptPower $adeptPower): AdeptPowerResource {
                return new AdeptPowerResource($adeptPower);
            },
        )->name('adept-powers.show');

        Route::get(
            'complex-forms',
            static function (): AnonymousResourceCollection {
                return ComplexFormResource::collection(ComplexForm::all())
                    ->additional(['links' => [
                        'self' => route('shadowrun6e.complex-forms.index'),
                    ]]);
            },
        )->name('complex-forms.index');
        Route::get(
            'complex-forms/{complex_form}',
            static function (ComplexForm $complexForm): ComplexFormResource {
                return new ComplexFormResource($complexForm);
            },
        )->name('complex-forms.show');

        Route::get(
            'echoes',
            static function (): AnonymousResourceCollection {
                return EchoResource::collection(ResonanceEcho::all())
                    ->additional(['links' => [
                        'self' => route('shadowrun6e.echoes.index'),
                    ]]);
            },
        )->name('echoes.index');
        Route::get(
            'echoes/{echo}',
            static function (ResonanceEcho $echo): EchoResource {
                return new EchoResource($echo);
            },
        )->name('echoes.show');

        Route::get(
            'mentor-spirits',
            static function (): AnonymousResourceCollection {
                return MentorSpiritResource::collection(MentorSpirit::all())
                    ->additional(['links' => [
                        'self' => route('shadowrun6e.mentor-spirits.index'),
                    ]]);
            },
        )->name('mentor-spirits.index');
        Route::get(
            'mentor-spirits/{spirit}',
            static function (MentorSpirit $spirit): MentorSpiritResource {
                return new MentorSpiritResource($spirit);
            },
        )->name('mentor-spirits.show');

        Route::get(
            'metamagics',
            static function (): AnonymousResourceCollection {
                return MetamagicResource::collection(Metamagic::all())
                    ->additional(['links' => [
                        'self' => route('shadowrun6e.metamagics.index'),
                    ]]);
            },
        )->name('metamagics.index');
        Route::get(
            'metamagics/{magic}',
            static function (Metamagic $magic): MetamagicResource {
                return new MetamagicResource($magic);
            },
        )->name('metamagics.show');

        Route::get(
            'programs',
            static function (): AnonymousResourceCollection {
                return ProgramResource::collection(Program::all())
                    ->additional(['links' => [
                        'self' => route('shadowrun6e.programs.index'),
                    ]]);
            },
        )->name('programs.index');
        Route::get(
            'programs/{program}',
            static function (Program $program): ProgramResource {
                return new ProgramResource($program);
            },
        )->name('programs.show');

        Route::get(
            'qualities',
            static function (): AnonymousResourceCollection {
                return QualityResource::collection(Quality::all())
                    ->additional(['links' => [
                        'self' => route('shadowrun6e.qualities.index'),
                    ]]);
            },
        )->name('qualities.index');
        Route::get(
            'qualities/{quality}',
            static function (Quality $quality): QualityResource {
                return new QualityResource($quality);
            },
        )->name('qualities.show');

        Route::get(
            'races',
            static function (): AnonymousResourceCollection {
                return RaceResource::collection(Race::all())
                    ->additional(['links' => [
                        'self' => route('shadowrun6e.races.index'),
                    ]]);
            },
        )->name('races.index');
        Route::get(
            'races/{race}',
            static function (Race $race): RaceResource {
                return new RaceResource($race);
            },
        )->name('races.show');

        Route::get(
            'rituals',
            static function (): AnonymousResourceCollection {
                return RitualResource::collection(Ritual::all())
                    ->additional(['links' => [
                        'self' => route('shadowrun6e.rituals.index'),
                    ]]);
            },
        )->name('rituals.index');
        Route::get(
            'rituals/{ritual}',
            static function (Ritual $ritual): RitualResource {
                return new RitualResource($ritual);
            },
        )->name('rituals.show');

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

        Route::get(
            'spells',
            static function (): AnonymousResourceCollection {
                return SpellResource::collection(Spell::all())
                    ->additional(['links' => [
                        'self' => route('shadowrun6e.spells.index'),
                    ]]);
            },
        )->name('spells.index');
        Route::get(
            'spells/{spell}',
            static function (Spell $spell): SpellResource {
                return new SpellResource($spell);
            },
        )->name('spells.show');

        Route::get(
            'spirits',
            static function (): AnonymousResourceCollection {
                return SpiritResource::collection(Spirit::all())
                    ->additional(['links' => [
                        'self' => route('shadowrun6e.spirits.index'),
                    ]]);
            },
        )->name('spirits.index');
        Route::get(
            'spirits/{spirit}',
            static function (Request $request, Spirit $spirit): SpiritResource {
                if (0 !== $request->integer('force')) {
                    $spirit->force = $request->integer('force');
                }
                return new SpiritResource($spirit);
            },
        )->name('spirits.show');
    });
