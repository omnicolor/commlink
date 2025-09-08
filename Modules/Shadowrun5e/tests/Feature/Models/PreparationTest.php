<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Preparation;
use Modules\Shadowrun5e\Models\Spell;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class PreparationTest extends TestCase
{
    /**
     * Subject under test.
     */
    private Preparation $preparation;

    /**
     * Set up the subject under test.
     */
    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->preparation = new Preparation();
    }

    /**
     * Test trying to set an invalid trigger.
     */
    public function testInvalidTrigger(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Invalid alchemical trigger');
        $this->preparation->setTrigger('foo');
    }

    /**
     * Test the setters.
     */
    public function testSetters(): void
    {
        $this->preparation->setDate('2019-06-28')
            ->setPotency(6)
            ->setSpell(new Spell('control-emotions'))
            ->setTrigger('time');
        self::assertSame('2019-06-28', $this->preparation->date);
        self::assertSame(6, $this->preparation->potency);
        self::assertSame(
            'Control Emotions',
            (string)$this->preparation->spell
        );
        self::assertSame('time', $this->preparation->trigger);
    }

    /**
     * Test setting the spell by ID.
     */
    public function testSetSpellId(): void
    {
        $this->preparation->setSpellId('control-emotions');
        self::assertSame('Control Emotions', (string)$this->preparation->spell);
    }
}
