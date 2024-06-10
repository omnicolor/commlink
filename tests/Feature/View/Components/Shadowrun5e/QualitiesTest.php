<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\View\Components\Shadowrun5e\Qualities;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class QualitiesTest extends TestCase
{
    /**
     * Test charGen property with an existing character.
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
