<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Rolls;

use App\Models\Channel;
use App\Rolls\Roll;
use Exception;
use Modules\Cyberpunkred\Models\TarotCard;
use Modules\Cyberpunkred\Models\TarotDeck;
use Omnicolor\Slack\Attachments\TextAttachment;
use Omnicolor\Slack\Exceptions\SlackException;
use Omnicolor\Slack\Response;
use Override;

use function assert;
use function count;
use function explode;
use function sprintf;

use const PHP_EOL;

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

        $args = explode(' ', $content);
        if (1 !== count($args) && 'shuffle' === $args[1]) {
            $this->shuffle = true;
            $this->deck->shuffle();
            $this->deck->save();
            return;
        }

        try {
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
        // Constructor verifies the campaign has been set.
        assert(null !== $this->campaign);

        $decks = TarotDeck::findForCampaign($this->campaign);
        foreach ($decks as $deck) {
            if ($deck instanceof TarotDeck) {
                $this->deck = $deck;
                return;
            }
        }

        // No deck was found, make a new one.
        $this->deck = new TarotDeck();
        $this->deck->campaign_id = $this->campaign->id;
        $this->deck->shuffle();
    }

    #[Override]
    public function forSlack(): Response
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }

        if ($this->shuffle) {
            $attachment = new TextAttachment(
                sprintf('%s shuffled the deck', $this->username),
                '',
                TextAttachment::COLOR_INFO,
                count($this->deck) . ' cards remain',
            );
            // @phpstan-ignore method.deprecated
            return (new Response())
                ->addAttachment($attachment)
                ->sendToChannel();
        }

        $attachment = new TextAttachment(
            $this->username . ' drew ' . $this->card,
            $this->card->getDescription() . PHP_EOL . PHP_EOL
                . '*Effect:* '
                . str_replace('||', PHP_EOL, $this->card->getEffect()),
            TextAttachment::COLOR_INFO,
            count($this->deck) . ' cards remain',
        );

        // @phpstan-ignore method.deprecated
        return (new Response())
            ->addAttachment($attachment)
            ->sendToChannel();
    }

    #[Override]
    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        if ($this->shuffle) {
            return sprintf('**%s shuffled the tarot deck**', $this->username);
        }

        return $this->username . ' drew **' . $this->card . '**'
            . PHP_EOL . PHP_EOL . $this->card->getDescription() . PHP_EOL
            . PHP_EOL . '**Effect:** '
            . str_replace('||', PHP_EOL, $this->card->getEffect());
    }

    #[Override]
    public function forIrc(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        if ($this->shuffle) {
            return sprintf('%s shuffled the tarot deck', $this->username);
        }

        return $this->username . ' drew ' . $this->card . PHP_EOL
            . PHP_EOL . $this->card->getDescription() . PHP_EOL
            . PHP_EOL . 'Effect: '
            . str_replace('||', PHP_EOL, $this->card->getEffect());
    }
}
