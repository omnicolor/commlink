<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;

use function config;

/**
 * A normal channel is on a chat server (Slack, Discord, etc). A WebChannel is
 * kind of a hack to allow using a Roll object from an API or web page, but
 * still getting the broadcasting. There's no concept of a ChatUser with
 * a WebChannel, but there should be a Character that is viewed.
 */
class WebChannel extends Channel
{
    protected Character $character;

    public function character(): Character
    {
        return $this->character;
    }

    /**
     * @return array<int, Character>
     */
    public function characters(): array
    {
        return [$this->character];
    }

    public static function findForWebhook(
        string $guild_id,
        string $webhook_id,
    ): ?self {
        throw new LogicException('WebChannels do not have webhooks');
    }

    public function initiatives(): HasMany
    {
        throw new LogicException('WebChannels do not have initiatives');
    }

    public function serverName(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                return config('app.name');
            },
        );
    }

    public function system(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                return $this->character->system;
            },
            set: function (): string {
                throw new LogicException(
                    'WebChannels use the character\'s system'
                );
            },
        );
    }

    public function type(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                return 'web';
            },
            set: function (): string {
                throw new LogicException('WebChannel types can not be set');
            },
        );
    }

    public function setCharacter(Character $character): self
    {
        $this->character = $character;
        return $this;
    }
}
