<?php

declare(strict_types=1);

namespace App\Models\Slack;

class ActionAttachment extends Attachment
{
    /**
     * @var array<int, array<string, string>>
     */
    protected array $actions = [];

    public function __construct(
        protected string $title,
        protected string $text,
        protected string $color,
        protected ?string $footer,
        protected ?string $callback_id,
    ) {
    }

    public function addAction(
        string $name,
        string $text,
        string $value,
        string $type = 'button'
    ): self {
        $this->actions[] = [
            'name' => $name,
            'text' => $text,
            'type' => $type,
            'value' => $value,
        ];
        return $this;
    }

    public function toArray(): array
    {
        if (null === $this->footer) {
            return [
                'callback_id' => $this->callback_id,
                'color' => $this->color,
                'text' => $this->text,
                'title' => $this->title,
                'actions' => $this->actions,
            ];
        }
        return [
            'callback_id' => $this->callback_id,
            'color' => $this->color,
            'footer' => $this->footer,
            'text' => $this->text,
            'title' => $this->title,
            'actions' => $this->actions,
        ];
    }
}
