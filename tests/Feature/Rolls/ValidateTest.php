<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls;

use App\Enums\ChannelType;
use App\Events\UserLinked;
use App\Models\Channel;
use App\Models\ChatUser;
use App\Rolls\Validate;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Omnicolor\Slack\Exceptions\SlackException;
use Omnicolor\Slack\Headers\Header;
use Omnicolor\Slack\Sections\Text;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function sprintf;

#[Medium]
final class ValidateTest extends TestCase
{
    use WithFaker;

    #[Group('slack')]
    public function testWrongNumberOfArguments(): void
    {
        Event::fake();

        /** @var Channel */
        $channel = Channel::factory()->make(['type' => ChannelType::Slack]);
        $channel->user = 'U' . Str::random(10);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            'To link your Commlink user, go to the settings page '
                . '(%s/settings/chat-users) and copy the command listed there '
                . 'for this server. If the server isn\'t listed, follow the '
                . 'instructions there to add it. You\'ll need to know your '
                . 'server ID (`%s`) and your user ID (`%s`).',
            config('app.url'),
            $channel->server_id,
            $channel->user,
        ));

        (new Validate('validate', $this->faker->userName(), $channel))
            ->forSlack();

        Event::assertNotDispatched(UserLinked::class);
    }

    #[Group('discord')]
    public function testInvalidHash(): void
    {
        Event::fake();

        $channel = Channel::factory()->make(['type' => ChannelType::Discord]);
        $channel->user = 'U' . Str::random(10);

        // Valid chat user, but not the right hash.
        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
        ]);

        $response = (new Validate(
            'validate invalid',
            $this->faker->userName(),
            $channel
        ))
            ->forDiscord();
        self::assertSame(
            sprintf(
                'We couldn\'t find a %s registration for this server and your '
                    . 'user. Go to the settings page (%s/settings/chat-users) '
                    . 'and copy the command listed there for this server. If '
                    . 'the server isn\'t listed, follow the instructions '
                    . 'there to add it. You\'ll need to know your server ID '
                    . '(`%s`) and your user ID (`%s`).',
                config('app.name'),
                config('app.url'),
                $channel->server_id,
                $channel->user,
            ),
            $response,
        );
        Event::assertNotDispatched(UserLinked::class);
    }

    #[Group('irc')]
    public function testAlreadyValidated(): void
    {
        Event::fake();
        $username = $this->faker->userName();

        $channel = Channel::factory()->create(['type' => ChannelType::Irc]);
        $channel->user = $username;

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'verified' => true,
        ]);

        $response = (new Validate(
            sprintf('validate %s', $chatUser->verification),
            $username,
            $channel,
        ))
            ->forIrc();
        self::assertSame(
            'It looks like you\'re already verified!',
            $response,
        );
        Event::assertNotDispatched(UserLinked::class);
    }

    #[Group('slack')]
    public function testValidateSlack(): void
    {
        Event::fake();
        $username = $this->faker->userName();

        $channel = Channel::factory()->create(['type' => ChannelType::Slack]);
        $channel->user = $username;

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'verified' => false,
        ]);

        $response = (new Validate(
            sprintf('validate %s', $chatUser->verification),
            $username,
            $channel,
        ))
            ->forSlack()
            ->jsonSerialize();

        self::assertSame(
            (new Header('Verified!'))->jsonSerialize(),
            $response['blocks'][0],
        );
        self::assertSame(
            (new Text(sprintf(
                'Your %s account has been linked with this user. You only need '
                    . 'to do this once for this server, no matter how many '
                    . 'different channels you play in.',
                config('app.name'),
            )))->jsonSerialize(),
            $response['blocks'][1],
        );
        Event::assertDispatched(UserLinked::class);
    }

    #[Group('discord')]
    public function testValidateDiscord(): void
    {
        Event::fake();
        $username = $this->faker->userName();

        $channel = Channel::factory()->create(['type' => ChannelType::Discord]);
        $channel->user = $username;

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'verified' => false,
        ]);

        $response = (new Validate(
            sprintf('validate %s', $chatUser->verification),
            $username,
            $channel,
        ))
            ->forDiscord();

        self::assertSame(
            sprintf(
                'Your %s account has been linked with this user. You only need '
                    . 'to do this once for this server, no matter how many '
                    . 'different channels you play in.',
                config('app.name'),
            ),
            $response,
        );
        Event::assertDispatched(UserLinked::class);
    }

    #[Group('irc')]
    public function testValidateIrc(): void
    {
        Event::fake();
        $username = $this->faker->userName();

        /** @var Channel $channel */
        $channel = Channel::factory()->create(['type' => ChannelType::Irc]);
        $channel->user = $username;

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'verified' => false,
        ]);

        $response = (new Validate(
            sprintf('validate %s', $chatUser->verification),
            $username,
            $channel,
        ))
            ->forIrc();

        self::assertSame(
            sprintf(
                'Your %s account has been linked with this user. You only need '
                    . 'to do this once for this server, no matter how many '
                    . 'different channels you play in.',
                config('app.name'),
            ),
            $response,
        );
        Event::assertDispatched(UserLinked::class);
    }
}
