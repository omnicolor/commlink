<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls;

use App\Enums\ChannelType;
use App\Events\ChannelLinked;
use App\Models\Channel;
use App\Models\ChatUser;
use App\Models\User;
use App\Rolls\Register;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Omnicolor\Slack\Exceptions\SlackException;
use Omnicolor\Slack\Headers\Header;
use Omnicolor\Slack\Sections\Text;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function implode;
use function sprintf;

#[Medium]
final class RegisterTest extends TestCase
{
    use WithFaker;

    #[Group('slack')]
    public function testRegisterWithoutSystem(): void
    {
        Event::fake();

        $channel = new Channel(['type' => ChannelType::Slack]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'To register a channel, use `register [system]`, where system is a '
                . 'system code: '
                . implode(', ', array_keys(config('commlink.systems')))
        );
        (new Register('register', $this->faker->userName(), $channel))
            ->forSlack();

        Event::assertNotDispatched(ChannelLinked::class);
    }

    #[Group('discord')]
    public function testRegisterAlreadyRegistered(): void
    {
        Event::fake();

        $channel = new Channel([
            'system' => 'shadowrun5e',
            'type' => ChannelType::Discord,
        ]);

        $response = (new Register(
            'register capers',
            $this->faker->userName(),
            $channel,
        ))
            ->forDiscord();
        self::assertSame(
            'This channel is already registered for "shadowrun5e"',
            $response,
        );

        Event::assertNotDispatched(ChannelLinked::class);
    }

    #[Group('irc')]
    public function testRegisterInvalidSystem(): void
    {
        Event::fake();

        $channel = new Channel(['type' => ChannelType::Irc]);

        $response = (new Register(
            'register invalid',
            $this->faker->userName(),
            $channel,
        ))
            ->forIrc();
        self::assertSame(
            '"invalid" is not a valid system code. Use register <system>, '
                . 'where system is one of: '
                . implode(', ', array_keys(config('commlink.systems'))),
            $response,
        );

        Event::assertNotDispatched(ChannelLinked::class);
    }

    #[Group('slack')]
    public function testRegisteringWithoutRegisteredUser(): void
    {
        Event::fake();

        $channel = new Channel(['type' => ChannelType::Slack]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            'You must have already created an account on %s '
                . '(%s/settings/chat-users) and linked it to this server '
                . 'before you can register a channel to a specific system.',
            config('app.name'),
            config('app.url'),
        ));
        (new Register('register capers', $this->faker->userName(), $channel))
            ->forSlack();

        Event::assertNotDispatched(ChannelLinked::class);
    }

    #[Group('slack')]
    public function testRegisterInSlack(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $channel = new Channel([
            'channel_id' => 'C' . Str::random(10),
            'server_id' => 'T' . Str::random(10),
            'type' => ChannelType::Slack,
        ]);
        $channel->username = 'Test User';

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        $response = (new Register(
            'register capers',
            $this->faker->userName(),
            $channel
        ))
            ->forSlack()
            ->jsonSerialize();

        self::assertSame(
            (new Header('Registered'))->jsonSerialize(),
            $response['blocks'][0],
        );
        self::assertSame(
            (new Text(
                'Test User has registered this channel for the "Capers" system.'
            ))->jsonSerialize(),
            $response['blocks'][1],
        );

        Event::assertDispatched(ChannelLinked::class);
    }

    #[Group('discord')]
    public function testRegisterInDiscord(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $channel = new Channel([
            'channel_id' => 'C' . Str::random(10),
            'server_id' => 'T' . Str::random(10),
            'type' => ChannelType::Discord,
        ]);
        $channel->username = 'Test User';

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        $response = (new Register(
            'register capers',
            $this->faker->userName(),
            $channel
        ))
            ->forDiscord();

        self::assertSame(
            'Test User has registered this channel for the "Capers" system.',
            $response,
        );

        Event::assertDispatched(ChannelLinked::class);
    }

    #[Group('irc')]
    public function testRegisterInIrc(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $channel = new Channel([
            'channel_id' => 'C' . Str::random(10),
            'server_id' => 'T' . Str::random(10),
            'type' => ChannelType::Irc,
        ]);
        $channel->username = 'Test User';

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        $response = (new Register(
            'register capers',
            $this->faker->userName(),
            $channel
        ))
            ->forIrc();

        self::assertSame(
            'Test User has registered this channel for the "Capers" system.',
            $response,
        );

        Event::assertDispatched(ChannelLinked::class);
    }
}
