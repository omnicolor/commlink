<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Slack;

use App\Models\Slack\Field;

/**
 * Tests for Slack Field class.
 * @covers \App\Models\Slack\Field
 * @group slack
 */
final class FieldTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test toArray() with minimum amount of data.
     * @test
     */
    public function testToArrayMinimum(): void
    {
        $field = new Field('Title', 'Value');
        $expected = ['title' => 'Title', 'value' => 'Value', 'short' => true];
        self::assertSame($expected, $field->toArray());
    }

    /**
     * Test toArray() setting the short field.
     * @test
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
