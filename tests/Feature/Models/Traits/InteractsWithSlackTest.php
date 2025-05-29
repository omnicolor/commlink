<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Traits;

use App\Enums\ChannelType;
use App\Models\Channel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

use function sprintf;

#[Group('slack')]
#[Small]
final class InteractsWithSlackTest extends TestCase
{
    protected const string API_CHANNELS = 'https://slack.com/api/conversations.info';
    protected const string API_TEAMS = 'https://slack.com/api/auth.teams.list';
    protected const string API_USERS = 'https://slack.com/api/users.info';

    protected Channel $mock;

    public function setUp(): void
    {
        parent::setUp();
        $this->mock = new Channel(['type' => ChannelType::Slack]);
    }

    /**
     * Test getting a Slack channel's name if the API call fails.
     */
    public function testGetChannelNameCallFails(): void
    {
        $url = sprintf('%s?channel=C00', self::API_CHANNELS);
        Http::fake([
            $url => Http::response([], 500),
        ]);
        self::assertNull($this->mock->getSlackChannelName('C00'));
    }

    /**
     * Test getting a Slack channel's name if the Slack call fails auth.
     */
    public function testGetChannelNameCallErrors(): void
    {
        $url = sprintf('%s?channel=C0000', self::API_CHANNELS);
        Http::fake([
            $url => Http::response([
                'ok' => false,
                'error' => 'not_authed',
            ]),
        ]);
        self::assertNull($this->mock->getSlackChannelName('C0000'));
    }

    /**
     * Test getting a Slack channel's name.
     */
    public function testGetChannelName(): void
    {
        $url = sprintf('%s?channel=a', self::API_CHANNELS);
        Http::fake([
            $url => Http::response([
                'ok' => true,
                'channel' => [
                    'name' => 'Channel NAME',
                ],
            ]),
        ]);
        self::assertSame('Channel NAME', $this->mock->getSlackChannelName('a'));
    }

    /**
     * Test getting a Slack server's name if the Slack API call fails.
     */
    public function testGetServerNameSlackCallFails(): void
    {
        Http::fake([
            self::API_TEAMS => Http::response([
                'ok' => false,
                'error' => 'not_authed',
            ]),
        ]);
        self::assertNull($this->mock->getSlackTeamName('aaa'));
    }

    /**
     * Test getting a Slack server's name if we don't have it, and none of the
     * teams returned match.
     */
    public function testGetServerNameSlackNoMatches(): void
    {
        Http::fake([
            self::API_TEAMS => Http::response([
                'ok' => true,
                'teams' => [
                    [
                        'id' => Str::random(9),
                        'name' => Str::random(20),
                    ],
                ],
            ]),
        ]);
        self::assertNull($this->mock->getSlackTeamName('foo'));
    }

    /**
     * Test getting a Slack server's name with a match.
     */
    public function testGetServerNameSlackMatch(): void
    {
        $teamId = 'T' . Str::random(10);
        $name = Str::random(15);
        Http::fake([
            self::API_TEAMS => Http::response([
                'ok' => true,
                'teams' => [
                    [
                        'id' => $teamId,
                        'name' => $name,
                    ],
                ],
            ]),
        ]);
        self::assertSame($name, $this->mock->getSlackTeamName($teamId));
    }

    /**
     * Test getting a Slack user's name if the call fails.
     */
    public function testGetUserNameCallFails(): void
    {
        $url = sprintf('%s?user=%s', self::API_USERS, 'UF0');
        Http::fake([
            $url => Http::response([], 500),
        ]);
        self::assertNull($this->mock->getSlackUserName('UF0'));
    }

    /**
     * Test getting a Slack user's name if the call errors.
     */
    public function testGetUserNameCallErrors(): void
    {
        $url = sprintf('%s?user=%s', self::API_USERS, 'UF00');
        Http::fake([
            $url => Http::response([
                'ok' => false,
                'error' => 'not_authed',
            ]),
        ]);
        self::assertNull($this->mock->getSlackUserName('UF00'));
    }

    /**
     * Test getting a Slack user's name.
     */
    public function testGetUserName(): void
    {
        $url = sprintf('%s?user=%s', self::API_USERS, 'UF000');
        Http::fake([
            $url => Http::response([
                'ok' => true,
                'user' => [
                    'name' => 'Batman',
                ],
            ]),
        ]);
        self::assertSame('Batman', $this->mock->getSlackUserName('UF000'));
    }
}
