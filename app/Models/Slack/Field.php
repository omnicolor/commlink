<?php

declare(strict_types=1);

namespace App\Models\Slack;

class Field
{
    /**
     * Whether the field is short or not.
     * @var bool
     */
    protected bool $short;

    /**
     * Title for the field.
     * @var string
     */
    protected string $title;

    /**
     * Value to put in the field.
     * @var string
     */
    protected string $value;

    /**
     * Constructor.
     * @param string $title
     * @param string $value
     * @param bool $short
     */
    public function __construct(
        string $title,
        string $value,
        bool $short = true
    ) {
        $this->title = $title;
        $this->value = $value;
        $this->short = $short;
    }

    /**
     * Return the field as an array.
     * @return array<string, string|bool>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'value' => $this->value,
            'short' => $this->short,
        ];
    }
}
