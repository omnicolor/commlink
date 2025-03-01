<?php

declare(strict_types=1);

namespace Modules\Alien\Rolls;

use App\Events\MessageReceived;
use App\Models\Channel;
use App\Rolls\Roll;
use Facades\App\Services\DiceService;
use Modules\Alien\Models\Injury as InjuryModel;
use Omnicolor\Slack\Attachments\TextAttachment;
use Omnicolor\Slack\Exceptions\SlackException;
use Omnicolor\Slack\Response;
use Override;

use function implode;
use function sprintf;

use const PHP_EOL;

class Injury extends Roll
{
    protected ?string $error = null;
    protected InjuryModel $injury;

    /** @var array<int, int> */
    protected array $rolls = [];

    public function __construct(
        string $content,
        string $username,
        Channel $channel,
        public ?MessageReceived $event = null,
    ) {
        parent::__construct($content, $username, $channel);

        $this->rolls[] = DiceService::rollOne(6);
        $this->rolls[] = DiceService::rollOne(6);

        $roll = (int)implode('', $this->rolls);
        $injury = InjuryModel::findByRoll($roll);
        if (null === $injury) {
            $this->error = sprintf('Injury result (%d) was invalid', $roll);
        } else {
            $this->injury = $injury;
        }
    }

    #[Override]
    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }
        return sprintf('**%s**', $this->formatTitle()) . PHP_EOL
            . $this->formatText() . PHP_EOL
            . 'Rolls: ' . implode(' ', $this->rolls);
    }

    #[Override]
    public function forIrc(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }
        return $this->formatTitle() . PHP_EOL
            . $this->formatText() . PHP_EOL
            . 'Rolls: ' . implode(' ', $this->rolls);
    }

    #[Override]
    public function forSlack(): Response
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }

        $attachment = new TextAttachment(
            $this->formatTitle(),
            $this->formatText(),
            TextAttachment::COLOR_DANGER,
            implode(' ', $this->rolls),
        );

        // @phpstan-ignore method.deprecated
        return (new Response())
            ->addAttachment($attachment)
            ->sendToChannel();
    }

    protected function formatTitle(): string
    {
        return sprintf(
            '%s gains %s injury: %s',
            $this->username,
            $this->injury->fatal ? 'a fatal' : 'an',
            (string)$this->injury,
        );
    }

    protected function formatText(): string
    {
        $text = 'Effects: ' . $this->injury->effects_text;
        if ($this->injury->fatal && null !== $this->injury->time_limit) {
            $text .= ' Make a Death Roll after ' . $this->injury->time_limit
                . ' or you will die.';
        }
        return $text;
    }
}
