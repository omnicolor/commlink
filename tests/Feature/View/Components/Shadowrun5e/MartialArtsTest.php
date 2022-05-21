<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\View\Components\Shadowrun5e\MartialArts;

/**
 * @small
 */
final class MartialArtsTest extends \Tests\TestCase
{
    /**
     * Test rendering a lack of martial arts for an existing character.
     * @test
     */
    public function testNoMartialArtsExistingCharacter(): void
    {
        $this->component(MartialArts::class, ['character' => new Character()])
            ->assertDontSee('martial arts')
            ->assertDontSee('Character has no martial arts')
            ->assertDontSee('free technique');
    }

    /**
     * Test rendering a lack of martial arts for a new character.
     * @test
     */
    public function testNoMartialArtsNewCharacter(): void
    {
        $this->component(
            MartialArts::class,
            ['character' => new PartialCharacter()]
        )
            ->assertSee('martial arts')
            ->assertSee('Character has no martial arts')
            ->assertDontSee('free technique');
    }

    /**
     * Test a new character with a style but no techniques.
     * @test
     */
    public function testNewCharacterWithStyleButNoTechnique(): void
    {
        $this->component(
            MartialArts::class,
            ['character' => new PartialCharacter([
                'martialArts' => [
                    'styles' => [
                        'aikido',
                    ],
                ],
            ])]
        )
            ->assertSee('martial arts')
            ->assertDontSee('Character has no martial arts')
            ->assertSee('Aikido')
            ->assertSee('free technique');
    }

    /**
     * Test an existing character that bought a style but didn't choose
     * a technique.
     * @test
     */
    public function testOldCharacterWithStyleButNoTechnique(): void
    {
        $this->component(
            MartialArts::class,
            ['character' => new Character([
                'martialArts' => [
                    'styles' => [
                        'aikido',
                    ],
                ],
            ])]
        )
            ->assertSee('martial arts')
            ->assertDontSee('Character has no martial arts')
            ->assertSee('Aikido')
            ->assertDontSee('free technique');
    }

    /**
     * Test an existing character that bought a style but didn't choose
     * a technique.
     * @test
     */
    public function testCharacterWithMartialArts(): void
    {
        $this->component(
            MartialArts::class,
            ['character' => new Character([
                'martialArts' => [
                    'styles' => [
                        'aikido',
                    ],
                    'techniques' => [
                        'called-shot-disarm',
                    ],
                ],
            ])]
        )
            ->assertSee('martial arts')
            ->assertDontSee('Character has no martial arts')
            ->assertDontSee('free technique')
            ->assertSee('Aikido')
            ->assertSee('Called Shot');
    }
}
