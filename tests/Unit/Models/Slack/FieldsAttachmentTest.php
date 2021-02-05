<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Slack;

use App\Models\Slack\Field;
use App\Models\Slack\FieldsAttachment;

/**
 * Tests for Slack FieldsAttachment.
 * @covers \App\Models\Slack\FieldsAttachment
 * @group slack
 */
final class FieldsAttachmentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test an empty fields attachment.
     * @test
     */
    public function testEmpty(): void
    {
        $attachment = new FieldsAttachment('Empty');
        $expected = [
            'title' => 'Empty',
            'fields' => [],
        ];
        self::assertSame($expected, $attachment->toArray());
    }

    /**
     * Test adding some fields.
     * @test
     */
    public function testWithFields(): void
    {
        $attachment = new FieldsAttachment('Full');
        $attachment->addField(new Field('Field 1', 'Value 1', false))
            ->addField(new Field('Field 2', 'Value 2', true));
        $expected = [
            'title' => 'Full',
            'fields' => [
                [
                    'title' => 'Field 1',
                    'value' => 'Value 1',
                    'short' => false,
                ],
                [
                    'title' => 'Field 2',
                    'value' => 'Value 2',
                    'short' => true,
                ],
            ],
        ];
        self::assertSame($expected, $attachment->toArray());
    }
}
