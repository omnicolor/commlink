<?php

declare(strict_types=1);

namespace App\Rolls\Capers;

use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Capers\StandardDeck;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;
use Exception;

/**
 * Handle a user asking to shuffle their deck.
 */
class Shuffle extends Roll
{
    /**
     * Deck to draw from.
     * @var StandardDeck
     */
    protected StandardDeck $deck;

    /**
     * Error to return instead of shuffling or drawing from the deck.
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
        if (!isset($this->campaign)) {
            $this->error = 'Decks for Capers require a linked Commlink '
                . 'campaign.';
            return;
        }
        if ('capers' !== $this->campaign->system) {
            $this->error = 'Capers-style card decks are only available for '
                . 'Capers campaigns.';
            return;
        }

        $this->findOrCreateDeck();
        $this->deck->shuffle();
        $this->deck->save();
    }

    /**
     * Try to find the current player's deck, or create a new one if the
     * current player doesn't have one.
     */
    protected function findOrCreateDeck(): void
    {
        try {
            $this->deck = StandardDeck::findForCampaignAndPlayer(
                // @phpstan-ignore-next-line
                $this->campaign,
                $this->username
            );
            return;
        } catch (Exception $ex) {
            // Ignore.
        }

        $this->deck = new StandardDeck();
        // @phpstan-ignore-next-line
        $this->deck->campaign_id = $this->campaign->id;
        $this->deck->character_id = $this->username;
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
            $this->username . ' shuffled their deck',
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

        return $this->username . ' shuffled their deck';
    }
}
