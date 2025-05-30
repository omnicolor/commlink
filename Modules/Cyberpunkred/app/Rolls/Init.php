<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Rolls;

use App\Events\InitiativeAdded;
use App\Models\Channel;
use App\Models\Initiative;
use App\Rolls\Roll;
use Facades\App\Services\DiceService;
use Modules\Cyberpunkred\Models\Character;
use Omnicolor\Slack\Attachments\TextAttachment;
use Omnicolor\Slack\Exceptions\SlackException;
use Omnicolor\Slack\Response;
use Override;

use function array_shift;
use function count;
use function explode;
use function optional;
use function sprintf;

use const PHP_EOL;

class Init extends Roll
{
    /**
     * Arguments passed to the roll.
     * @var array<int, string>
     */
    protected array $args = [];

    /**
     * Optional modifier for the character's initiative.
     */
    public int $modifier = 0;

    /**
     * Reflexes used to determine the character's initiative.
     */
    public int $reflexes;

    /**
     * Result of the die roll.
     */
    public int $roll;

    public ?string $error = null;

    /**
     * Initiative object created for the roll.
     */
    protected Initiative $initiative;

    public function __construct(
        string $content,
        string $username,
        Channel $channel,
    ) {
        parent::__construct($content, $username, $channel);

        $args = explode(' ', $content);

        // Remove 'init' from argument list.
        array_shift($args);
        $this->args = $args;

        if (null === $this->character && 0 === count($this->args)) {
            $this->error = 'Rolling initiative without a linked character '
                . 'requires your reflexes, and optionally any modififers: '
                . '`/roll init 8 -2` for a character with 8 REF and a wound '
                . 'modifier of -2';
            return;
        }
        $this->roll();
    }

    /**
     * Roll a d10 for initiative, notifying listeners of the results.
     */
    protected function roll(): void
    {
        // Rolls with a character attached shouldn't need to enter reflexes.
        if (isset($this->character)) {
            /** @var Character */
            $character = $this->character;
            $this->username = (string)$character;
            if (2 === count($this->args)) {
                // Get rid of the character's reflexes.
                array_shift($this->args);
            }
            $this->reflexes = $character->reflexes;
            if (1 === count($this->args)) {
                $this->modifier = (int)array_shift($this->args);
            }
        } else {
            if (1 <= count($this->args)) {
                $this->reflexes = (int)array_shift($this->args);
            }
            if (1 === count($this->args)) {
                $this->modifier = (int)array_shift($this->args);
            }
        }

        $this->roll = DiceService::rollOne(10);
        $this->initiative = Initiative::updateOrCreate(
            [
                'campaign_id' => optional($this->campaign)->id,
                'channel_id' => $this->channel->channel_id,
                'character_id' => optional($this->channel->character())->id,
                'character_name' => $this->username,
            ],
            ['initiative' => $this->roll + $this->reflexes + $this->modifier],
        );
        if (null !== $this->campaign) {
            InitiativeAdded::dispatch(
                $this->initiative,
                $this->campaign,
                $this->channel
            );
        }
    }

    protected function formatBody(): string
    {
        $extra = '';
        if (0 < $this->modifier) {
            $extra = ' + ' . $this->modifier;
        } elseif (0 > $this->modifier) {
            $extra = ' - ' . abs($this->modifier);
        }
        return sprintf(
            'Rolled: %d + %d%s = %d',
            $this->roll,
            $this->reflexes,
            $extra,
            $this->roll + $this->reflexes + $this->modifier,
        );
    }

    #[Override]
    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }
        return sprintf('**Initiative added for %s**', $this->username)
            . PHP_EOL . $this->formatBody();
    }

    #[Override]
    public function forIrc(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }
        return sprintf('Initiative added for %s', $this->username)
            . PHP_EOL . $this->formatBody();
    }

    #[Override]
    public function forSlack(): Response
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }
        $attachment = new TextAttachment(
            sprintf('Initiative added for %s', $this->username),
            $this->formatBody(),
        );
        // @phpstan-ignore method.deprecated
        return (new Response())
            ->addAttachment($attachment)
            ->sendToChannel();
    }
}
