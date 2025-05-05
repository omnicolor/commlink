<?php

declare(strict_types=1);

namespace Modules\Transformers\Rolls;

use App\Models\Channel;
use App\Rolls\Roll;
use Facades\App\Services\DiceService;
use Omnicolor\Slack\Attachments\TextAttachment;
use Omnicolor\Slack\Response;
use Override;

use function array_shift;
use function explode;
use function implode;
use function sprintf;

use const PHP_EOL;

class Number extends Roll
{
    protected int $roll;
    protected int $statistic;
    protected bool $success;
    protected string $title;
    protected string $text;

    public function __construct(
        string $content,
        string $character,
        Channel $channel
    ) {
        parent::__construct($content, $character, $channel);

        $args = explode(' ', $content);
        $this->statistic = (int)array_shift($args);
        $this->description = implode(' ', $args);
        if ('' !== $this->description) {
            $this->description = sprintf(' for "%s"', $this->description);
        }

        $this->roll = DiceService::rollOne(10);
        $this->success = $this->roll < $this->statistic;
        if ($this->success) {
            $this->title = sprintf(
                '%s rolled a success%s',
                $this->username,
                $this->description
            );
            $this->text = sprintf('%d < %d', $this->roll, $this->statistic);
        } else {
            $this->title = sprintf(
                '%s rolled a failure%s',
                $this->username,
                $this->description
            );
            $this->text = sprintf('%d >= %d', $this->roll, $this->statistic);
        }
    }

    #[Override]
    public function forDiscord(): string
    {
        return sprintf('**%s**', $this->title) . PHP_EOL . $this->text;
    }

    #[Override]
    public function forIrc(): string
    {
        return $this->title . PHP_EOL . $this->text;
    }

    #[Override]
    public function forSlack(): Response
    {
        $attachment = new TextAttachment(
            $this->title,
            $this->text,
            $this->success ? TextAttachment::COLOR_SUCCESS : TextAttachment::COLOR_DANGER,
        );

        // @phpstan-ignore method.deprecated
        return (new Response())
            ->addAttachment($attachment)
            ->sendToChannel();
    }
}
