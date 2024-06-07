<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Traits;

use App\Models\Traits\InteractsWithSlack;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

use function sprintf;

/**
 * Tests for the InteractsWithSlackTest.
 * @group slack
 * @small
 */
final class InteractsWithSlackTest extends TestCase
{
    protected const API_CHANNELS = 'https://slack.com/api/conversations.info';
    protected const API_TEAMS = 'https://slack.com/api/auth.teams.list';
    protected const API_USERS = 'https://slack.com/api/users.info';

    protected MockObject $mock;

    /**
     * Set up the subject under test.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->mock = $this->getMockForTrait(InteractsWithSlack::class);
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
        // @phpstan-ignore-next-line
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
        // @phpstan-ignore-next-line
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
        // @phpstan-ignore-next-line
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
        // @phpstan-ignore-next-line
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
        // @phpstan-ignore-next-line
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
        // @phpstan-ignore-next-line
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
        // @phpstan-ignore-next-line
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
        // @phpstan-ignore-next-line
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
        // @phpstan-ignore-next-line
        self::assertSame('Batman', $this->mock->getSlackUserName('UF000'));
    }
}
