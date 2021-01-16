<?php

declare(strict_types=1);

namespace Tests\Unit\Shadowrun5E;

use App\Models\Shadowrun5E\ComplexForm;

/**
 * Tests for ComplexForm object.
 */
final class ComplexFormTest extends \Tests\TestCase
{
    /**
     * Test trying to load an invalid Complex Form.
     * @test
     */
    public function testLoadInvalid(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Complex Form ID "foo" is invalid');
        new ComplexForm('foo');
    }

    /**
     * Test toString.
     * @test
     */
    public function testToString(): void
    {
        $form = new ComplexForm('cleaner');
        self::assertSame('Cleaner', (string)$form);
    }

    /**
     * Test setting the level.
     * @test
     */
    public function testSetLevel(): void
    {
        $form = new ComplexForm('cleaner');
        self::assertNull($form->level);
        $form->setLevel(5);
        self::assertSame(5, $form->level);
    }

    /**
     * Test getFade() without setting the level.
     * @test
     */
    public function testGetFadeNoLevel(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Level has not been set');
        $form = new ComplexForm('cleaner');
        $form->getFade();
    }

    /**
     * Test getFade().
     * @test
     */
    public function testGetFade(): void
    {
        $form = new ComplexForm('cleaner', 3);
        self::assertSame(4, $form->getFade());
        $form->setLevel(6);
        self::assertSame(7, $form->getFade());
    }
}
