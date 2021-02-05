<?php

declare(strict_types=1);

namespace App\Models\Slack;

/**
 * Simple text attachment for a Slack Response.
 */
class TextAttachment extends Attachment
{
    /**
     * Color for the left bar.
     * @var string
     */
    protected string $color;

    /**
     * Text to include in the response.
     * @var string
     */
    protected string $text;

    /**
     * Title for the attachment.
     * @var string
     */
    protected string $title;

    /**
     * Constructor.
     * @param string $title
     * @param string $text
     * @param string $color
     */
    public function __construct(
        string $title,
        string $text,
        string $color = 'success'
    ) {
        $this->color = $color;
        $this->text = $text;
        $this->title = $title;
    }

    /**
     * Return the attachment as an array.
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'color' => $this->color,
            'text' => $this->text,
            'title' => $this->title,
        ];
    }
}
