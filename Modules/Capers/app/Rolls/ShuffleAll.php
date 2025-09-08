<?php

declare(strict_types=1);

namespace Modules\Capers\Rolls;

use App\Models\Campaign;
use App\Models\Channel;
use App\Rolls\Roll;
use Illuminate\Support\Facades\DB;
use Modules\Capers\Models\StandardDeck;
use Omnicolor\Slack\Attachments\TextAttachment;
use Omnicolor\Slack\Exceptions\SlackException;
use Omnicolor\Slack\Response;
use Override;

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
     */
    protected ?string $error = null;

    public function __construct(
        string $content,
        string $username,
        Channel $channel
    ) {
        parent::__construct($content, $username, $channel);
        if (!$this->campaign instanceof Campaign) {
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
        $rows = DB::table('decks')->where('campaign_id', $this->campaign?->id)
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

    #[Override]
    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        return 'The Gamemaster shuffled all decks';
    }

    #[Override]
    public function forIrc(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        return 'The Gamemaster shuffled all decks';
    }

    #[Override]
    public function forSlack(): Response
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }

        $attachment = new TextAttachment(
            'The Gamemaster shuffled all decks',
            '',
            TextAttachment::COLOR_INFO,
        );

        // @phpstan-ignore method.deprecated
        return (new Response())
            ->addAttachment($attachment)
            ->sendToChannel();
    }
}
