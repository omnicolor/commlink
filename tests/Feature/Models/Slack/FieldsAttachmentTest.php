<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Slack;

use App\Models\Slack\Field;
use App\Models\Slack\FieldsAttachment;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('slack')]
#[Small]
final class FieldsAttachmentTest extends TestCase
{
    /**
     * Test an empty fields attachment.
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
