<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Subversion;

use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Rolls\Subversion\Help;
use Tests\TestCase;

use function json_decode;
use function sprintf;

/**
 * Tests for getting help in a Subversion channel.
 * @group subversion
 * @medium
 */
final class HelpTest extends TestCase
{
    /**
     * Test getting help via Slack for a channel as an unregistered user.
     * @group slack
     */
    public function testHelpSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make([
            'system' => 'subversion',
            'type' => Channel::TYPE_SLACK,
        ]);
        $response = (new Help('', 'username', $channel))->forSlack();
        $response = json_decode((string)$response);
        self::assertSame(
            sprintf('%s - Subversion', config('app.name')),
            $response->attachments[0]->title
        );
        self::assertSame(
            'Note for unregistered users:',
            $response->attachments[1]->title
        );
    }

    /**
     * @group discord
     */
    public function testHelpInDiscordWithCharacter(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create([
            'system' => 'subversion',
            'type' => Channel::TYPE_DISCORD,
        ]);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'system' => 'subversion',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Help('', 'username', $channel))->forDiscord();
        self::assertStringContainsString(
            sprintf('Subversion commands (as %s)', (string)$character),
            $response
        );
    }

    public function testHelpIrc(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make([
            'system' => 'subversion',
            'type' => Channel::TYPE_IRC,
        ]);
        $response = (new Help('', 'username', $channel))->forIrc();
        self::assertStringContainsString(
            sprintf('%s - Subversion', config('app.name')),
            $response,
        );
    }
}
