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
use Exception;

/**
 * Handle a user asking for a card.
 */
class Draw extends Roll
{
    /**
     * Card that was drawn.
     * @var Card
     */
    protected Card $card;

    /**
     * Deck to draw from.
     * @var StandardDeck
     */
    protected StandardDeck $deck;

    /**
     * Extra text to add to the output.
     * @var string
     */
    protected string $extra = '';

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

        try {
            $this->card = $this->deck->drawOne();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            return;
        }

        $args = \explode(' ', $content);
        if (1 !== count($args)) {
            $this->extra = 'for ' . implode(' ', array_slice($args, 1));
        }

        $this->deck->save();
    }

    /**
     * Try to find the current player's deck, or create a new one if the
     * current player doesn't have one.
     */
    protected function findOrCreateDeck(): void
    {
        // @codeCoverageIgnoreStart
        if (!isset($this->campaign)) {
            return;
        }
        // @codeCoverageIgnoreEnd
        try {
            $this->deck = StandardDeck::findForCampaignAndPlayer(
                $this->campaign,
                $this->username
            );
            return;
        } catch (Exception $ex) {
            // Ignore.
        }

        $this->deck = new StandardDeck();
        $this->deck->campaign_id = $this->campaign->id;
        $this->deck->character_id = $this->username;
        $this->deck->shuffle();
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
            $this->username . ' drew the ' . (string)$this->card,
            $this->extra,
            TextAttachment::COLOR_INFO,
        );
        $attachment->addFooter(count($this->deck) . ' cards remain');
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

        return $this->username . ' drew the **' . (string)$this->card . '**'
            . \PHP_EOL . $this->extra;
    }
}
