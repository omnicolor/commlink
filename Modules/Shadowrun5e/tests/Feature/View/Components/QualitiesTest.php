<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\View\Components;

use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\PartialCharacter;
use Modules\Shadowrun5e\View\Components\Qualities;
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
        $view = $this->component(
            Qualities::class,
            ['character' => new Character()]
        )
            ->assertDontSee('No qualities found.');
        self::assertInstanceOf(Qualities::class, $view->component);
        self::assertFalse($view->component->charGen);
    }

    /**
     * Test charGen property with an in-progress character.
     */
    public function testCharGenInprogressCharacter(): void
    {
        $view = $this->component(
            Qualities::class,
            ['character' => new PartialCharacter()]
        )
            ->assertSee('No qualities found.');
        self::assertInstanceOf(Qualities::class, $view->component);
        self::assertTrue($view->component->charGen);
    }

    /**
     * Test rendering a quality.
     */
    public function testRenderQuality(): void
    {
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
        self::assertInstanceOf(Qualities::class, $view->component);
        self::assertTrue($view->component->charGen);
    }
}
