<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Slack;

use App\Models\Slack\Field;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('slack')]
#[Small]
final class FieldTest extends TestCase
{
    /**
     * Test toArray() with minimum amount of data.
     */
    public function testToArrayMinimum(): void
    {
        $field = new Field('Title', 'Value');
        $expected = ['title' => 'Title', 'value' => 'Value', 'short' => true];
        self::assertSame($expected, $field->toArray());
    }

    /**
     * Test toArray() setting the short field.
     */
    public function testToArray(): void
    {
        $field = new Field('A Title', 'A Value', false);
        $expected = [
            'title' => 'A Title',
            'value' => 'A Value',
            'short' => false,
        ];
        self::assertSame($expected, $field->toArray());
    }
}
