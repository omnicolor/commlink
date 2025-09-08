<?php

declare(strict_types=1);

namespace Modules\Capers\Rolls;

use App\Models\Campaign;
use App\Models\Channel;
use App\Rolls\Roll;
use Exception;
use Modules\Capers\Models\StandardDeck;
use Omnicolor\Slack\Attachments\TextAttachment;
use Omnicolor\Slack\Exceptions\SlackException;
use Omnicolor\Slack\Response;
use Override;

use function assert;

/**
 * Handle a user asking to shuffle their deck.
 */
class Shuffle extends Roll
{
    /**
     * Deck to draw from.
     */
    protected StandardDeck $deck;

    /**
     * Error to return instead of shuffling or drawing from the deck.
     */
    protected ?string $error = null;

    public function __construct(
        string $content,
        string $username,
        Channel $channel,
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
        // Constructor sets an error if $this->campaign is not set.
        assert($this->campaign instanceof Campaign);
        try {
            $this->deck = StandardDeck::findForCampaignAndPlayer(
                $this->campaign,
                $this->username
            );
            return;
        } catch (Exception) {
            // Ignore.
        }

        $this->deck = new StandardDeck();
        $this->deck->campaign_id = $this->campaign->id;
        $this->deck->character_id = $this->username;
    }

    #[Override]
    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        return $this->username . ' shuffled their deck';
    }

    #[Override]
    public function forIrc(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        return $this->username . ' shuffled their deck';
    }

    #[Override]
    public function forSlack(): Response
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }

        $attachment = new TextAttachment(
            $this->username . ' shuffled their deck',
            '',
            TextAttachment::COLOR_INFO,
        );

        // @phpstan-ignore method.deprecated
        return (new Response())
            ->addAttachment($attachment)
            ->sendToChannel();
    }
}
