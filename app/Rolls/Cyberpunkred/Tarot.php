<?php

declare(strict_types=1);

namespace App\Rolls\Cyberpunkred;

use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Cyberpunkred\TarotCard;
use App\Models\Cyberpunkred\TarotDeck;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;
use Exception;

/**
 * Handle a user asking for a tarot card.
 */
class Tarot extends Roll
{
    /**
     * Tarot card drawn.
     */
    protected TarotCard $card;

    /**
     * Tarot deck to draw from.
     */
    protected TarotDeck $deck;

    /**
     * Error to return instead of shuffling or drawing from the deck.
     */
    protected ?string $error = null;

    /**
     * Whether the user had asked to shuffle the deck.
     */
    protected bool $shuffle = false;

    public function __construct(
        string $content,
        string $username,
        Channel $channel
    ) {
        parent::__construct($content, $username, $channel);
        if (null === $this->campaign) {
            $this->error = 'Tarot decks require a linked Commlink campaign.';
            return;
        }
        if ('cyberpunkred' !== $this->campaign->system) {
            $this->error = 'Night City Tarot only available for Cyberpunk Red '
                . 'campaigns.';
            return;
        }
        if (
            null === $this->campaign->options
            || !isset($this->campaign->options['nightCityTarot'])
            || false === $this->campaign->options['nightCityTarot']
        ) {
            $this->error = 'Night City Tarot not enabled for campaign.';
            return;
        }
        $this->findOrCreateDeck();

        $args = \explode(' ', $content);
        if (1 !== count($args) && 'shuffle' === $args[1]) {
            $this->shuffle = true;
            $this->deck->shuffle();
            $this->deck->save();
            return;
        }

        try {
            // @phpstan-ignore-next-line
            $this->card = $this->deck->drawOne();
        } catch (Exception $ex) {
            $this->error = $ex->getMessage();
            return;
        }
        $this->deck->save();
    }

    /**
     * Try to find the Night City Tarot deck for the campaign, or create a new
     * one.
     */
    protected function findOrCreateDeck(): void
    {
        // @phpstan-ignore-next-line
        $decks = TarotDeck::findForCampaign($this->campaign);
        foreach ($decks as $deck) {
            if ($deck instanceof TarotDeck) {
                $this->deck = $deck;
                return;
            }
        }

        // No deck was found, make a new one.
        $this->deck = new TarotDeck();
        // @phpstan-ignore-next-line
        $this->deck->campaign_id = $this->campaign->id;
        $this->deck->shuffle();
    }

    /**
     * Return the roll formatted for Slack.
     */
    public function forSlack(): SlackResponse
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }

        if ($this->shuffle) {
            $attachment = new TextAttachment(
                sprintf('%s shuffled the deck', $this->username),
                '',
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

        $attachment = new TextAttachment(
            $this->username . ' drew ' . (string)$this->card,
            $this->card->getDescription() . \PHP_EOL . \PHP_EOL
                . '*Effect:* '
                . str_replace('||', \PHP_EOL, $this->card->getEffect()),
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
     */
    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        if ($this->shuffle) {
            return sprintf('**%s shuffled the tarot deck**', $this->username);
        }

        return $this->username . ' drew **' . (string)$this->card . '**'
            . \PHP_EOL . \PHP_EOL . $this->card->getDescription() . \PHP_EOL
            . \PHP_EOL . '**Effect:** '
            . str_replace('||', \PHP_EOL, $this->card->getEffect());
    }

    /**
     * Return the roll formatted for IRC.
     */
    public function forIrc(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        if ($this->shuffle) {
            return sprintf('%s shuffled the tarot deck', $this->username);
        }

        return $this->username . ' drew ' . (string)$this->card . \PHP_EOL
            . \PHP_EOL . $this->card->getDescription() . \PHP_EOL
            . \PHP_EOL . 'Effect: '
            . str_replace('||', \PHP_EOL, $this->card->getEffect());
    }
}
