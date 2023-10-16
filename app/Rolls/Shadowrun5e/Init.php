<?php

declare(strict_types=1);

namespace App\Rolls\Shadowrun5e;

use App\Events\InitiativeAdded;
use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Initiative;
use App\Models\Shadowrun5e\ForceTrait;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;
use Facades\App\Services\DiceService;
use RuntimeException;

class Init extends Roll
{
    use ForceTrait;

    protected const MAX_DICE = 5;
    protected const REQUIRED_PIPS = 6;

    /** @var array<int, int> */
    protected array $dice = [];
    protected ?string $error = null;
    /** @pvar non-negative-int */
    protected int $initiativeScore = 0;
    protected int $initiativeDice = 1;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        string $content,
        string $username,
        Channel $channel
    ) {
        parent::__construct($content, $username, $channel);

        if ($this->isGm()) {
            $this->handleGmCommands();
            return;
        }
        if (null === $this->campaign) {
            // For channels without a campaign, anyone can pretend to be the GM.
            $this->handleGmCommands();

            if ('' !== $this->title) {
                // Handling the GM commands found a GM command.
                return;
            }

            // It looks like the command wasn't a GM command, we'll continue
            // with normal user initiative.
            $this->error = null;
        }
        if (isset($this->character)) {
            /** @var \App\Models\Shadowrun5e\Character */
            $character = $this->character;
            $this->initiativeScore = $character->initiative_score;
            $this->initiativeDice = $character->initiative_dice;
        } else {
            try {
                $this->parseArgs();
            } catch (RuntimeException $ex) {
                $this->error = $ex->getMessage();
                return;
            }
        }

        try {
            $this->roll();
        } catch (RuntimeException $ex) {
            $this->error = $ex->getMessage();
            return;
        }

        $initiative = Initiative::updateOrCreate(
            [
                'campaign_id' => optional($this->campaign)->id,
                'channel_id' => $this->channel->id,
                'character_id' => optional($this->channel->character())->id,
                'character_name' => $this->username,
            ],
            ['initiative' => $this->initiativeScore + array_sum($this->dice)],
        );
        if (null !== $this->campaign) {
            InitiativeAdded::dispatch(
                $initiative,
                $this->campaign,
                $this->channel
            );
        }
    }

    protected function handleGmCommands(): void
    {
        $args = \explode(' ', $this->content);

        // Remove 'init' from argument list.
        \array_shift($args);

        switch ($args[0] ?? '') {
            case 'clear':
                if (null !== $this->campaign) {
                    Initiative::forCampaign($this->campaign)->delete();
                } else {
                    Initiative::forChannel($this->channel)->delete();
                }
                $this->title = 'Initiative cleared';
                $this->text = 'The GM has cleared the initiative tracker.';
                break;
            case 'start':
                if (null !== $this->campaign) {
                    Initiative::forCampaign($this->campaign)->delete();
                } else {
                    Initiative::forChannel($this->channel)->delete();
                }
                $this->title = 'Roll initiative!';
                $this->text = 'Type `/roll init` if your character is linked, '
                    . 'or `/roll init A+Bd6` where A is your initiative score '
                    . 'and B is the number of initiative dice your character '
                    . 'gets.';
                break;
            default:
                $this->error = 'That doesn\'t appear to be a valid GM '
                    . 'initiative command';
                break;
        }
    }

    /**
     * Pull the dynamic part of the text out.
     *
     * For an expression like '10+9d6+27', would pull out and return '9d6'.
     */
    protected function getDynamicPart(string $string): string
    {
        $matches = [];
        \preg_match('/(\d+)d(\d+)/', $string, $matches);
        if (!isset($matches[0])) {
            throw new RuntimeException();
        }
        return $matches[0];
    }

    /**
     * Convert a string like '1d6' into its two parts: 1 and 6.
     * @return array<int, int>
     */
    protected function getDiceAndPips(string $dynamicPart): array
    {
        $dicePart = \explode('d', $dynamicPart);
        return [(int)$dicePart[0], (int)$dicePart[1]];
    }

    /**
     * Pull out the initiative score and dice from the user's input, or set an
     * error message to display.
     */
    protected function parseArgs(): void
    {
        $args = \explode(' ', $this->content);

        // Remove 'init' from argument list.
        \array_shift($args);

        if (2 === count($args)) {
            if (!ctype_digit($args[0]) || !ctype_digit($args[1])) {
                throw new RuntimeException(
                    'Initiative is rolled like "/roll init 12 2" or "/roll '
                        . 'init 12+2d6"'
                );
            }
            $this->initiativeScore = (int)$args[0];
            $this->initiativeDice = (int)$args[1];
            return;
        }
        if (1 !== count($args)) {
            throw new RuntimeException(
                'Initiative is rolled like "/roll init 12 2" or "/roll init '
                    . '12+2d6"'
            );
        }

        if (str_contains($args[0], '+')) {
            $parts = explode('+', $args[0]);
            if (!ctype_digit($parts[0])) {
                throw new RuntimeException('Initiative score must be a number');
            }
            $this->initiativeScore = (int)$parts[0];
            try {
                $dynamicPart = $this->getDynamicPart($parts[1]);
                [$dice, $pips] = $this->getDiceAndPips($dynamicPart);
            } catch (RuntimeException) {
                throw new RuntimeException(
                    'Initiative is rolled like "/roll init 12 2" or "/roll '
                        . 'init 12+2d6"'
                );
            }
            if (self::REQUIRED_PIPS !== $pips) {
                throw new RuntimeException(
                    'Only six-sided dice can be used for initiative'
                );
            }
            $this->initiativeDice = $dice;
            return;
        }

        if (!ctype_digit($args[0])) {
            throw new RuntimeException(
                'Initiative is rolled like "/roll init 12 2" or "/roll init '
                    . '12+2d6"'
            );
        }
        $this->initiativeScore = (int)$args[0];
    }

    protected function roll(): void
    {
        if (self::MAX_DICE < $this->initiativeDice) {
            throw new RuntimeException(
                'You can\'t roll more than five initiative dice'
            );
        }
        $this->dice = DiceService::rollMany($this->initiativeDice, 6);
    }

    public function forSlack(): SlackResponse
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }
        $response = new SlackResponse(channel: $this->channel);
        if ('' !== $this->title) {
            $response->addAttachment(new TextAttachment(
                $this->title,
                $this->text,
                TextAttachment::COLOR_INFO,
                $this->footer,
            ));
            return $response->sendToChannel();
        }
        $response->addAttachment(new TextAttachment(
            sprintf('Rolling initiative for %s', $this->username),
            sprintf(
                '%d + %dd6 = %d',
                $this->initiativeScore,
                $this->initiativeDice,
                $this->initiativeScore + array_sum($this->dice),
            ),
            TextAttachment::COLOR_INFO,
            'Rolls: ' . implode(' ', $this->dice)
        ));
        return $response->sendToChannel();
    }

    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }
        if ('' !== $this->title) {
            return \sprintf('**%s**', $this->title) . \PHP_EOL . $this->text;
        }
        return sprintf('**Rolling initiative for %s**', $this->username)
            . \PHP_EOL
            . sprintf(
                '%1$d + %2$dd6 = %1$d + %4$s = %3$d',
                $this->initiativeScore,
                $this->initiativeDice,
                $this->initiativeScore + array_sum($this->dice),
                implode(' + ', $this->dice)
            );
    }
}
