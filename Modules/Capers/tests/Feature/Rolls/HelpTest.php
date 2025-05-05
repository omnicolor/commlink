<?php

declare(strict_types=1);

namespace Modules\Capers\Tests\Feature\Rolls;

use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Modules\Capers\Rolls\Help;
use Omnicolor\Slack\Attachment;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function sprintf;

use const PHP_EOL;

#[Group('capers')]
#[Medium]
final class HelpTest extends TestCase
{
    use WithFaker;

    /**
     * Test asking for help as an unlinked user.
     */
    #[Group('slack')]
    public function testHelpNoLinkedUser(): void
    {
        $campaign = Campaign::factory()->create(['system' => 'capers']);

        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'capers',
        ]);
        $channel->username = $this->faker->name;

        $response = (new Help('help', $channel->username, $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_INFO,
                'text' => 'Commlink is a Slack/Discord bot that lets you '
                    . 'track virtual card decks for the Capers RPG system.'
                    . PHP_EOL
                    . '· `draw [text]` - Draw a card, with optional text '
                    . '(automatics, perception, etc)' . PHP_EOL
                    . '· `shuffle` - Shuffle your deck' . PHP_EOL,
                'title' => sprintf('%s - Capers', config('app.name')),
            ],
            $response['attachments'][0],
        );
        self::assertSame(
            [
                'color' => Attachment::COLOR_INFO,
                'text' => 'No character linked',
                'title' => 'Player',
            ],
            $response['attachments'][1]
        );
    }

    /**
     * Test asking for help as a Gamemaster.
     */
    #[Group('discord')]
    public function testHelpGamemaster(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'capers',
        ]);

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
     */
    #[Group('discord')]
    public function testHelpPlayerNoCharacter(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create(['system' => 'capers']);

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
     */
    #[Group('irc')]
    public function testHelpPlayerCharacter(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create(['system' => 'capers']);

        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'system' => 'capers',
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

        $character = Character::factory()->create([
            'name' => $this->faker->name,
            'system' => 'capers',
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Help('help', $channel->username, $channel))
            ->forIrc();
        self::assertStringContainsString(
            'Player' . PHP_EOL . $character->name,
            $response
        );

        $character->delete();
    }
}
