<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\ComplexForm;
use RuntimeException;
use Tests\TestCase;

/**
 * Tests for ComplexForm object.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class ComplexFormTest extends TestCase
{
    /**
     * Test trying to load an invalid Complex Form.
     */
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Complex Form ID "foo" is invalid');
        new ComplexForm('foo');
    }

    /**
     * Test toString.
     */
    public function testToString(): void
    {
        $form = new ComplexForm('cleaner');
        self::assertSame('Cleaner', (string)$form);
    }

    /**
     * Test setting the level.
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
     */
    public function testGetFadeNoLevel(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Level has not been set');
        $form = new ComplexForm('cleaner');
        $form->getFade();
    }

    /**
     * Test getFade().
     */
    public function testGetFade(): void
    {
        $form = new ComplexForm('cleaner', 3);
        self::assertSame(4, $form->getFade());
        $form->setLevel(6);
        self::assertSame(7, $form->getFade());
    }
}
