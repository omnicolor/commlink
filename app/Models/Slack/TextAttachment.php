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
     * Optional footer for the attachment.
     * @var ?string
     */
    protected ?string $footer = null;

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
     * @param ?string $footer
     */
    public function __construct(
        string $title,
        string $text,
        string $color = self::COLOR_SUCCESS,
        ?string $footer = null
    ) {
        $this->color = $color;
        $this->footer = $footer;
        $this->text = $text;
        $this->title = $title;
    }

    /**
     * Add a footer to the attachment.
     * @param string $footer
     * @return TextAttachment
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
        if (is_null($this->footer)) {
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
