<?php

declare(strict_types=1);

namespace Modules\Capers\Rolls;

use App\Models\Card;
use App\Models\Channel;
use App\Rolls\Roll;
use Exception;
use Modules\Capers\Models\StandardDeck;
use Omnicolor\Slack\Attachments\TextAttachment;
use Omnicolor\Slack\Exceptions\SlackException;
use Omnicolor\Slack\Response;
use Override;

use function array_slice;
use function assert;
use function count;
use function explode;
use function implode;

use const PHP_EOL;

/**
 * Handle a user asking for a card.
 */
class Draw extends Roll
{
    /**
     * Card that was drawn.
     */
    protected Card $card;

    /**
     * Deck to draw from.
     */
    protected StandardDeck $deck;

    /**
     * Extra text to add to the output.
     */
    protected string $extra = '';

    /**
     * Error to return instead of shuffling or drawing from the deck.
     */
    protected ?string $error = null;

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

        $args = explode(' ', $content);
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
        // Constructor makes sure $this->campaign is set.
        assert(null !== $this->campaign);
        try {
            $this->deck = StandardDeck::findForCampaignAndPlayer(
                $this->campaign,
                $this->username,
            );
            return;
        } catch (Exception) {
            // Ignore.
        }

        $this->deck = new StandardDeck();
        $this->deck->campaign_id = $this->campaign->id;
        $this->deck->character_id = $this->username;
        $this->deck->shuffle();
    }

    #[Override]
    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        return $this->username . ' drew the **' . $this->card . '**'
            . PHP_EOL . $this->extra;
    }

    #[Override]
    public function forIrc(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        return $this->username . ' drew the ' . $this->card . PHP_EOL
            . $this->extra;
    }

    #[Override]
    public function forSlack(): Response
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }

        $attachment = new TextAttachment(
            $this->username . ' drew the ' . $this->card,
            $this->extra,
            TextAttachment::COLOR_INFO,
            count($this->deck) . ' cards remain'
        );

        // @phpstan-ignore method.deprecated
        return (new Response())
            ->addAttachment($attachment)
            ->sendToChannel();
    }
}
