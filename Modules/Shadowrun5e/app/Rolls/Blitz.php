<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Rolls;

use App\Events\InitiativeAdded;
use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Initiative;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;
use Modules\Shadowrun5e\Models\Character;

use function sprintf;

use const PHP_EOL;

/**
 * Blitz Shadowrun 5E initiative using five initiative dice.
 *
 * Roll the maximum of five initiative dice for a single turn.
 * @property Character $character
 * @psalm-api
 */
class Blitz extends Init
{
    /**
     * @var array<int, int>
     */
    protected array $dice = [];
    protected ?string $error = null;
    protected int $initiativeDice = 5;
    protected int $initiativeScore = 0;

    /**
     * @phpstan-ignore-next-line
     */
    public function __construct(
        string $content,
        string $username,
        Channel $channel
    ) {
        Roll::__construct($content, $username, $channel);

        if ($this->isGm()) {
            $this->error = 'GMs can\'t blitz initiative';
            return;
        }

        if (null === $this->character) {
            $this->error = 'You must have a character linked to blitz initiative';
            return;
        }

        $character = $this->character;
        if (null === $character->edgeCurrent) {
            /** @psalm-suppress UndefinedMagicPropertyAssignment */
            $character->edgeCurrent = $character->edge ?? 0;
        }
        if (0 === $character->edgeCurrent) {
            $this->error = 'It looks like you\'re out of edge!';
            return;
        }

        $this->initiativeScore = $character->initiative_score;

        $this->roll();

        /** @psalm-suppress UndefinedMagicPropertyAssignment */
        $character->edgeCurrent--;
        $character->save();

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

    public function forSlack(): SlackResponse
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }
        $response = new SlackResponse(channel: $this->channel);
        $response->addAttachment(new TextAttachment(
            sprintf('%s blitzed', $this->username),
            sprintf(
                '%d + 5d6 = %d',
                $this->initiativeScore,
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
        return sprintf('**%s blitzed**', $this->username)
            . PHP_EOL
            . sprintf(
                '%1$d + 5d6 = %1$d + %3$s = %2$d',
                $this->initiativeScore,
                $this->initiativeScore + array_sum($this->dice),
                implode(' + ', $this->dice)
            );
    }

    public function forIrc(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }
        return sprintf('%s blitzed', $this->username)
            . PHP_EOL
            . sprintf(
                '%1$d + 5d6 = %1$d + %3$s = %2$d',
                $this->initiativeScore,
                $this->initiativeScore + array_sum($this->dice),
                implode(' + ', $this->dice)
            );
    }
}
