<?php

declare(strict_types=1);

namespace Modules\Startrekadventures\Tests\Feature\Rolls;

use App\Models\Campaign;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Modules\Startrekadventures\Models\Character;
use Modules\Startrekadventures\Rolls\Help;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('startrekadventures')]
#[Medium]
final class HelpTest extends TestCase
{
    use WithFaker;

    #[Group('slack')]
    public function testHelpNoLinkedUserSlack(): void
    {
        $channel = Channel::factory()->make([
            'system' => 'startrekadventures',
        ]);
        $channel->username = $this->faker->name;

        $response = (new Help('help', $channel->username, $channel))
            ->forSlack()
            ->jsonSerialize();
        self::assertArrayHasKey('attachments', $response);
        self::assertArrayHasKey(0, $response['attachments']);
        self::assertArrayHasKey('text', $response['attachments'][0]);
        self::assertStringContainsString(
            'roll Star Trek Adventures dice',
            $response['attachments'][0]['text'],
        );
        self::assertStringContainsString(
            'Note for unregistered users',
            $response['attachments'][1]['title'],
        );
    }

    #[Group('discord')]
    public function testHelpNoLinkedUserDiscord(): void
    {
        $channel = Channel::factory()->make([
            'system' => 'startrekadventures',
        ]);
        $channel->username = $this->faker->name;

        $response = (new Help('help', $channel->username, $channel))
            ->forDiscord();
        self::assertStringContainsString(
            'Your Discord user has not been linked',
            $response
        );
    }

    #[Group('discord')]
    public function testHelpGamemaster(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'startrekadventures',
        ]);

        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'startrekadventures',
            'type' => Channel::TYPE_DISCORD,
        ]);
        $channel->username = $this->faker->name;
        $channel->user = 'U' . Str::random(10);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'user_id' => $user,
            'verified' => true,
        ]);

        $response = (new Help('help', $channel->username, $channel))
            ->forDiscord();
        self::assertStringContainsString('Gamemaster commands', $response);
    }

    #[Group('discord')]
    public function testHelpPlayerNoCharacter(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create([
            'system' => 'startrekadventures',
        ]);

        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'startrekadventures',
            'type' => Channel::TYPE_DISCORD,
        ]);
        $channel->username = $this->faker->name;
        $channel->user = 'U' . Str::random(10);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'user_id' => $user,
            'verified' => true,
        ]);

        $response = (new Help('help', $channel->username, $channel))
            ->forDiscord();
        self::assertStringContainsString('link <characterId>', $response);
    }

    #[Group('discord')]
    public function testHelpPlayerCharacter(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create(['system' => 'startrekadventures']);

        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'system' => 'startrekadventures',
            'type' => Channel::TYPE_DISCORD,
        ]);
        $channel->username = $this->faker->name;
        $channel->user = 'U' . Str::random(10);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'user_id' => $user,
            'verified' => true,
        ]);

        $character = Character::factory()->create([]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Help('help', $channel->username, $channel))
            ->forDiscord();
        self::assertStringContainsString((string)$character->name, $response);

        $character->delete();
    }

    #[Group('irc')]
    public function testHelpIrc(): void
    {
        $campaign = Campaign::factory()->create([
            'system' => 'startrekadventures',
        ]);

        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'startrekadventures',
            'type' => Channel::TYPE_IRC,
        ]);
        $channel->username = $this->faker->name;
        $channel->user = 'U' . Str::random(10);

        $response = (new Help('help', $channel->username, $channel))
            ->forIrc();

        self::assertStringContainsString(
            'Your IRC user has not been linked',
            $response,
        );
    }
}
