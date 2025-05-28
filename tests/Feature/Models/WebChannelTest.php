<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Enums\ChannelType;
use App\Models\Character;
use App\Models\WebChannel;
use LogicException;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

use function config;

#[Small]
final class WebChannelTest extends TestCase
{
    public function testSetCharacter(): void
    {
        $channel = new WebChannel();
        $character = new Character();
        $channel->setCharacter($character);
        self::assertSame($character, $channel->character());
    }

    public function testCharacters(): void
    {
        $character = new Character();
        $channel = new WebChannel();
        $channel->setCharacter($character);
        self::assertSame([$character], $channel->characters());
    }

    public function testGetChatUser(): void
    {
        $channel = new WebChannel();
        self::assertNull($channel->getChatUser());
    }

    public function testInitiatives(): void
    {
        $channel = new WebChannel();
        self::expectException(LogicException::class);
        // @phpstan-ignore expr.resultUnused
        $channel->initiatives;
    }

    public function testCampaign(): void
    {
        $channel = new WebChannel();
        self::assertNull($channel->campaign);
    }

    public function testServerName(): void
    {
        $channel = new WebChannel();
        self::assertSame(config('app.name'), $channel->server_name);
    }

    public function testSetSystem(): void
    {
        $channel = new WebChannel();
        self::expectException(LogicException::class);
        $channel->system = 'avatar';
    }

    public function testGetSystem(): void
    {
        $character = new Character(['system' => 'alien']);
        $channel = new WebChannel();
        $channel->setCharacter($character);
        self::assertSame('alien', $channel->system);
    }

    public function testGetType(): void
    {
        $channel = new WebChannel();
        self::assertSame('web', $channel->type);
    }

    public function testSetType(): void
    {
        $channel = new WebChannel();
        self::expectException(LogicException::class);
        self::expectExceptionMessage('WebChannel types can not be set');
        $channel->type = ChannelType::Slack;
    }

    public function testFindForWebhook(): void
    {
        self::expectException(LogicException::class);
        WebChannel::findForWebhook('guild', 'webhook');
    }
}
