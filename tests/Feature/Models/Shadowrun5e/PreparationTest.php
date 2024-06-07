<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Preparation;
use App\Models\Shadowrun5e\Spell;
use RuntimeException;
use Tests\TestCase;

/**
 * Unit tests for Preparation class.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class PreparationTest extends TestCase
{
    /**
     * Subject under test.
     * @var Preparation
     */
    protected Preparation $preparation;

    /**
     * Set up the subject under test.
     */
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
        self::assertEquals('2019-06-28', $this->preparation->date);
        self::assertEquals(6, $this->preparation->potency);
        self::assertInstanceOf(Spell::class, $this->preparation->spell);
        self::assertEquals(
            'Control Emotions',
            (string)$this->preparation->spell
        );
        self::assertEquals('time', $this->preparation->trigger);
    }

    /**
     * Test setting the spell by ID.
     */
    public function testSetSpellId(): void
    {
        $this->preparation->setSpellId('control-emotions');
        self::assertInstanceOf(Spell::class, $this->preparation->spell);
        self::assertEquals('Control Emotions', (string)$this->preparation->spell);
    }
}
