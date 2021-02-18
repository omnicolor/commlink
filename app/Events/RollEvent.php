<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * A user rolled some generic dice.
 */
class RollEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Individual die results.
     * @var array<int, int>
     */
    public array $rolls;

    /**
     * Where the event was generated.
     * @var \App\Models\Slack\Channel
     */
    public $source;

    /**
     * Text of the event.
     * @var string
     */
    public string $text;

    /**
     * Title of the event.
     * @var string
     */
    public string $title;

    /**
     * Create a new event instance.
     * @param string $title
     * @param string $text
     * @param array<int, int> $rolls
     * @param mixed $source
     */
    public function __construct(
        string $title,
        string $text,
        array $rolls,
        mixed $source
    ) {
        $this->title = $title;
        $this->text = $text;
        $this->rolls = $rolls;
        $this->source = $source;
    }
}
