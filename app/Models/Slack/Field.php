<?php

declare(strict_types=1);

namespace App\Models\Slack;

class Field
{
    /**
     * Constructor.
     * @param string $title
     * @param string $value
     * @param bool $short
     */
    public function __construct(
        protected string $title,
        protected string $value,
        protected bool $short = true,
    ) {
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
