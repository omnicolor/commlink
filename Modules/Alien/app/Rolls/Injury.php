<?php

declare(strict_types=1);

namespace Modules\Alien\Rolls;

use App\Events\MessageReceived;
use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;
use Facades\App\Services\DiceService;
use Modules\Alien\Models\Injury as InjuryModel;

use function implode;
use function sprintf;

use const PHP_EOL;

/**
 * @psalm-suppress UnusedClass
 */
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

        /** @psalm-suppress UndefinedClass */
        $this->rolls[] = DiceService::rollOne(6);
        /** @psalm-suppress UndefinedClass */
        $this->rolls[] = DiceService::rollOne(6);

        $roll = (int)implode('', $this->rolls);
        $injury = InjuryModel::findByRoll($roll);
        if (null === $injury) {
            $this->error = sprintf('Injury result (%d) was invalid', $roll);
        } else {
            $this->injury = $injury;
        }
    }

    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }
        return sprintf('**%s**', $this->formatTitle()) . PHP_EOL
            . $this->formatText() . PHP_EOL
            . 'Rolls: ' . implode(' ', $this->rolls);
    }

    public function forIrc(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }
        return $this->formatTitle() . PHP_EOL
            . $this->formatText() . PHP_EOL
            . 'Rolls: ' . implode(' ', $this->rolls);
    }

    public function forSlack(): SlackResponse
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }

        $attachment = new TextAttachment(
            $this->formatTitle(),
            $this->formatText(),
            TextAttachment::COLOR_DANGER,
        );
        $attachment->addFooter(implode(' ', $this->rolls));
        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment)->sendToChannel();
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
