<?php

declare(strict_types=1);

namespace App\Models\Slack;

/**
 * Attachment that can be added to a Slack Response.
 */
abstract class Attachment
{
    public const COLOR_DANGER = 'danger';
    public const COLOR_INFO = '#439Fe0';
    public const COLOR_SUCCESS = 'good';
    public const COLOR_WARNING = 'warning';

    /**
     * @psalm-suppress PossiblyUnusedProperty
     */
    protected ?string $footer = null;

    /**
     * Add a footer to the attachment.
     * @param string $footer
     * @return Attachment
     */
    public function addFooter(string $footer): Attachment
    {
        $this->footer = $footer;
        return $this;
    }

    /**
     * Render the attachment as an array.
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;
}
