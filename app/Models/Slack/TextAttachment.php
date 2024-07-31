<?php

declare(strict_types=1);

namespace App\Models\Slack;

/**
 * Simple text attachment for a Slack Response.
 */
class TextAttachment extends Attachment
{
    /**
     * Constructor.
     * @param ?string $footer
     */
    public function __construct(
        protected string $title,
        protected string $text,
        protected string $color = self::COLOR_SUCCESS,
        protected ?string $footer = null,
    ) {
    }

    /**
     * Add a footer to the attachment.
     */
    public function addFooter(string $footer): TextAttachment
    {
        $this->footer = $footer;
        return $this;
    }

    /**
     * Return the attachment as an array.
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        if (null === $this->footer) {
            return [
                'color' => $this->color,
                'text' => $this->text,
                'title' => $this->title,
            ];
        }
        return [
            'color' => $this->color,
            'footer' => $this->footer,
            'text' => $this->text,
            'title' => $this->title,
        ];
    }
}
