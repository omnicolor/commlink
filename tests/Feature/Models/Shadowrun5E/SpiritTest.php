<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\Spirit;

/**
 * Tests for the spirit class.
 * @covers \App\Models\Shadowrun5E\Spirit
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class SpiritTest extends \Tests\TestCase
{
    /**
     * List of spirits.
     * @var array<string, array<string, mixed>>
     */
    private array $spirits;

    /**
     * Set up the data file's contents.
     */
    public function setUp(): void
    {
        parent::setUp();
        $filename = \Config::get('app.data_path.shadowrun5e') . 'spirits.php';
        $this->spirits ??= require $filename;
    }

    /**
     * Test a spirit that has not been given a force.
     * @test
     */
    public function testSpiritWithoutForce(): void
    {
        $spirit = new Spirit('air');
        self::assertEquals('F+3', $spirit->agility);
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Force has not been set');
        $spirit->getAgility();
    }

    /**
     * Test getting a spirit's attributes if they have a force.
     * @test
     */
    public function testSpiritWithForce(): void
    {
        $spirit = new Spirit('air', 5);
        self::assertEquals('Spirit of Air', $spirit->name);
        self::assertEquals('F+3', $spirit->agility);
        self::assertEquals(8, $spirit->getAgility());
        self::assertEquals('F-2', $spirit->body);
        self::assertEquals(3, $spirit->getBody());
        self::assertEquals(5, $spirit->getCharisma());
        self::assertEquals(2, $spirit->getEdge());
        self::assertEquals(5, $spirit->getEssence());
        self::assertEquals(5, $spirit->getIntuition());
        self::assertEquals(5, $spirit->getLogic());
        self::assertEquals(5, $spirit->getMagic());
        self::assertEquals(9, $spirit->getReaction());
        self::assertEquals(2, $spirit->getStrength());
        self::assertEquals(5, $spirit->getWillpower());
        self::assertEquals([10, 3], $spirit->getAstralInitiative());
        self::assertEquals([14, 2], $spirit->getInitiative());
    }

    /**
     * Test getting an attribute that is not part of a spirit.
     * @test
     */
    public function testGettingInvalidAttribute(): void
    {
        self::expectException(\BadMethodCallException::class);
        self::expectExceptionMessage(
            'Resonance is not an attribute of spirits'
        );
        $spirit = new Spirit('air', 5);
        $spirit->getResonance();
    }

    /**
     * Test trying to load an invalid spirit type.
     * @test
     */
    public function testInvalidSpirit(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Spirit ID "foo" is invalid');
        new Spirit('foo');
    }

    /**
     * Test the __toString method.
     * @test
     */
    public function testToString(): void
    {
        $spirit = new Spirit('air', 5);
        self::assertEquals('Spirit of Air', (string)$spirit);
    }

    /**
     * Test setting a spirit's force.
     * @test
     */
    public function testSetForce(): void
    {
        $spirit = new Spirit('air', 6);
        self::assertInstanceOf(Spirit::class, $spirit->setForce(3));
        self::assertEquals(3, $spirit->force);
    }

    /**
     * Test the data file for required fields.
     * @test
     */
    public function testDataFileRequiredFields(): void
    {
        $required = ['id', 'name', 'page', 'ruleset'];
        foreach ($this->spirits as $key => $spirit) {
            foreach ($required as $field) {
                self::assertArrayHasKey(
                    $field,
                    $spirit,
                    \sprintf('Spirit %s is missing field %s', $key, $field)
                );
            }
        }
    }

    /**
     * Test the data file for matching IDs.
     * @test
     */
    public function testDataFileSpiritIds(): void
    {
        foreach ($this->spirits as $key => $spirit) {
            self::assertEquals(
                $key,
                $spirit['id'],
                \sprintf(
                    'Spirit %s has mismatched key: %s',
                    $spirit['id'],
                    $key
                )
            );
        }
    }
}
