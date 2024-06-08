<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\StarTrekAdventures;

use App\Models\Campaign;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\StarTrekAdventures\Character;
use App\Models\User;
use App\Rolls\StarTrekAdventures\Help;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('star-trek-adventures')]
#[Medium]
final class HelpTest extends TestCase
{
    use WithFaker;

    #[Group('slack')]
    public function testHelpNoLinkedUserSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make([
            'system' => 'star-trek-adventures',
        ]);
        $channel->username = $this->faker->name;

        $response = (new Help('help', $channel->username, $channel))
            ->forSlack();
        $response = json_decode((string)$response);
        self::assertStringContainsString(
            'Star Trek Adventures',
            $response->attachments[0]->title
        );
        self::assertStringContainsString(
            'Note for unregistered users',
            $response->attachments[1]->title
        );
    }

    #[Group('discord')]
    public function testHelpNoLinkedUserDiscord(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make([
            'system' => 'star-trek-adventures',
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

        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'star-trek-adventures',
        ]);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'star-trek-adventures',
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

        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'system' => 'star-trek-adventures',
        ]);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'star-trek-adventures',
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

        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'star-trek-adventures']);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'system' => 'star-trek-adventures',
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
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

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
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'system' => 'star-trek-adventures',
        ]);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'star-trek-adventures',
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
