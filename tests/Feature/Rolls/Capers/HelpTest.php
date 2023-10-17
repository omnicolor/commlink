<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Capers;

use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\User;
use App\Rolls\Capers\Help;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Tests asking for help in a channel registered to the Capers RPG.
 * @group capers
 * @medium
 */
final class HelpTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test asking for help as an unlinked user.
     * @group slack
     * @test
     */
    public function testHelpNoLinkedUser(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'capers']);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
        ]);
        $channel->username = $this->faker->name;

        $response = (new Help('help', $channel->username, $channel))
            ->forSlack();
        self::assertSame(
            \sprintf('%s - Capers', config('app.name')),
            json_decode((string)$response)->attachments[0]->title
        );
        self::assertSame(
            'No character linked',
            json_decode((string)$response)->attachments[1]->text
        );
    }

    /**
     * Test asking for help as a Gamemaster.
     * @group discord
     * @test
     */
    public function testHelpGamemaster(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'capers',
        ]);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
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

    /**
     * Test asking for help as a registered player.
     * @group discord
     * @test
     */
    public function testHelpPlayerNoCharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'capers']);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
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
        self::assertStringContainsString('No character linked', $response);
    }

    /**
     * Test asking for help as a character.
     * @group irc
     * @test
     */
    public function testHelpPlayerCharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'capers']);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'system' => 'capers',
            'type' => Channel::TYPE_DISCORD,
        ]);
        $channel->username = $this->faker->name;
        $channel->user = 'U' . Str::random(10);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'user_id' => $user,
            'verified' => true,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'name' => $this->faker->name,
            'system' => 'capers',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Help('help', $channel->username, $channel))
            ->forIrc();
        self::assertStringContainsString(
            'Player' . \PHP_EOL . $character->name,
            $response
        );

        $character->delete();
    }
}
