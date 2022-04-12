<?php

declare(strict_types=1);

namespace App\Rolls\Capers;

use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Capers\StandardDeck;
use App\Models\Card;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;
use DB;
use Error;

/**
 * Handle a user asking everyone to shuffle their decks.
 */
class ShuffleAll extends Roll
{
    /**
     * Decks attached to the campaign.
     * @var array<int, StandardDeck>
     */
    protected array $decks = [];

    /**
     * Error to return instead of shuffling decks.
     * @var ?string
     */
    protected ?string $error = null;

    /**
     * Constructor.
     * @param string $content
     * @param string $username
     * @param Channel $channel
     */
    public function __construct(
        string $content,
        string $username,
        Channel $channel
    ) {
        parent::__construct($content, $username, $channel);
        if (null === $this->campaign) {
            $this->error = 'Decks for Capers require a linked Commlink '
                . 'campaign.';
            return;
        }
        if ('capers' !== $this->campaign->system) {
            $this->error = 'Capers-style card decks are only available for '
                . 'Capers campaigns.';
            return;
        }

        if (!$this->isGm()) {
            $this->error = 'You must be the game\'s GM to shuffle all decks';
            return;
        }

        $this->findAllDecks();
        foreach ($this->decks as $deck) {
            $deck->shuffle();
            $deck->save();
        }
    }

    /**
     * Try to find the current player's deck, or create a new one if the
     * current player doesn't have one.
     */
    protected function findAllDecks(): void
    {
        // @phpstan-ignore-next-line
        $rows = DB::table('decks')->where('campaign_id', $this->campaign->id)
            ->where('type', StandardDeck::class)
            ->get();
        foreach ($rows as $row) {
            /** @var StandardDeck */
            $deck = new $row->type();
            $deck->campaign_id = (int)$row->campaign_id;
            $deck->character_id = $row->character_id;
            $deck->currentCards = unserialize($row->cards);
            $deck->id = (int)$row->id;
            $this->decks[] = $deck;
        }
    }

    /**
     * Return the card formatted for Slack.
     * @return SlackResponse
     */
    public function forSlack(): SlackResponse
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }

        $attachment = new TextAttachment(
            'The Gamemaster shuffled all decks',
            '',
            TextAttachment::COLOR_INFO,
        );
        $response = new SlackResponse(
            '',
            SlackResponse::HTTP_OK,
            [],
            $this->channel
        );
        return $response->addAttachment($attachment)->sendToChannel();
    }

    /**
     * Return the roll formatted for Discord.
     * @return string
     */
    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        return 'The Gamemaster shuffled all decks';
    }
}
