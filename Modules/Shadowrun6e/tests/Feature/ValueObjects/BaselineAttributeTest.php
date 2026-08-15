<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\ValueObjects;

use Modules\Shadowrun6e\Models\Character;
use Modules\Shadowrun6e\ValueObjects\BaselineAttribute;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class BaselineAttributeTest extends TestCase
{
    public function testMinMaxWithNoQualities(): void
    {
        $attribute = new BaselineAttribute(1, 6, 'strength');
        self::assertSame(6, $attribute->getMaximum(new Character()));
        self::assertSame(1, $attribute->getMinimum());
    }

    public function testMinMaxWithPositiveQuality(): void
    {
        $character = new Character([
            'qualities' => [
                ['id' => 'exceptional-attribute-strength'],
            ],
        ]);
        $attribute = new BaselineAttribute(1, 6, 'strength');
        self::assertSame(7, $attribute->getMaximum($character));
        self::assertSame(1, $attribute->getMinimum());
        $attribute = new BaselineAttribute(1, 6, 'body');
        self::assertSame(6, $attribute->getMaximum($character));
        self::assertSame(1, $attribute->getMinimum());
    }

    public function testMinMaxWithNegativeQuality(): void
    {
        $character = new Character([
            'qualities' => [
                ['id' => 'impaired-body-1'],
            ],
        ]);
        $attribute = new BaselineAttribute(1, 6, 'strength');
        self::assertSame(6, $attribute->getMaximum($character));
        self::assertSame(1, $attribute->getMinimum());
        $attribute = new BaselineAttribute(1, 6, 'body');
        self::assertSame(5, $attribute->getMaximum($character));
        self::assertSame(1, $attribute->getMinimum());
    }
}
