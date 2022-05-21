<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\View\Components\Shadowrun5e\Qualities;

/**
 * @small
 */
final class QualitiesTest extends \Tests\TestCase
{
    /**
     * Test charGen property with an existing character.
     * @test
     */
    public function testCharGenExistingCharacter(): void
    {
        /** @var Qualities */
        $view = $this->component(
            Qualities::class,
            ['character' => new Character()]
        )
            ->assertDontSee('No qualities found.');
        self::assertFalse($view->charGen);
    }

    /**
     * Test charGen property with an in-progress character.
     * @test
     */
    public function testCharGenInprogressCharacter(): void
    {
        /** @var Qualities */
        $view = $this->component(
            Qualities::class,
            ['character' => new PartialCharacter()]
        )
            ->assertSee('No qualities found.');
        self::assertTrue($view->charGen);
    }

    /**
     * Test rendering a quality.
     * @test
     */
    public function testRenderQuality(): void
    {
        /** @var Qualities */
        $view = $this->component(
            Qualities::class,
            [
                'character' => new PartialCharacter([
                    'qualities' => [
                        ['id' => 'alpha-junkie'],
                    ],
                ]),
            ]
        )
            ->assertSee('Alpha Junkie')
            ->assertDontSee('No qualities found.');
        self::assertTrue($view->charGen);
    }
}
