<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\ComplexForm;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
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
