<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\SlackLink;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Str;

/**
 * Tests for the settings controller.
 * @group controllers
 */
final class SettingsControllerTest extends \Tests\TestCase
{
    /**
     * Test an unauthenticated request.
     * @test
     */
    public function testUnauthenticated(): void
    {
        $this->get('/settings')->assertRedirect('/login');
    }

    /**
     * Test authenticated no Slack Links.
     * @test
     */
    public function testNoSlackLinks(): void
    {
        $user = User::factory()->make();
        $this->actingAs($user)
            ->get('/settings')
            ->assertSee('You don\'t have any linked Slack teams!', false);
    }

    /**
     * Test an authenticated request that has some Slack teams linked.
     * @test
     */
    public function testWithSlackLinks(): void
    {
        $user = User::factory()->create();
        $slackLink = SlackLink::factory()->create([
            'slack_team' => Str::random(10),
            'slack_user' => Str::random(10),
            'user_id' => $user->id,
        ]);
        $this->actingAs($user)
            ->get('/settings')
            ->assertDontSee('You don\'t have any linked Slack teams!', false)
            ->assertSee(sprintf(
                '%s (%s)',
                $slackLink->team_name,
                $slackLink->slack_team
            ))
            ->assertSee(sprintf(
                '%s (%s)',
                $slackLink->user_name,
                $slackLink->slack_user
            ));
    }

    /**
     * Test trying to create a Slack Link with incomplete info.
     * @test
     */
    public function testCreateSlackLinkErrors(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->post('/settings/link-slack', [])
            ->assertStatus(302)
            ->assertSessionHasErrors();
        self::assertSame(
            ['Enter your Slack User ID'],
            session('errors')->get('slack-user')
        );
        self::assertSame(
            ['Enter your Slack Team ID'],
            session('errors')->get('slack-team')
        );
    }

    /**
     * Test creating a Slack link if the Slack team and user aren't found.
     * @test
     */
    public function testCreateSlackLinkNoTeamNoUser(): void
    {
        $user = User::factory()->create();
        $teamId = Str::random(10);
        $userId = Str::random(10);
        Http::fake([
            'slack.com/api/auth.teams.list' => Http::response([
                'ok' => false,
                'error' => 'not_authed',
            ]),
            sprintf('slack.com/api/users.info?user=%s', $userId) => Http::response([
                'ok' => false,
                'error' => 'not_authed',
            ]),
        ]);
        $this->actingAs($user)
            ->post(
                '/settings/link-slack',
                ['slack-team' => $teamId, 'slack-user' => $userId]
            )
            ->assertStatus(302)
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(
            'slack_links',
            [
                'character_id' => null,
                'slack_team' => $teamId,
                'team_name' => null,
                'slack_user' => $userId,
                'user_name' => null,
                'user_id' => $user->id,
            ]
        );
    }

    /**
     * Test creating a Slack link if the Slack team is found, but not the user.
     * @test
     */
    public function testCreateSlackLinkNoUser(): void
    {
        $user = User::factory()->create();
        $teamId = Str::random(10);
        $teamName = Str::random(12);
        $userId = Str::random(10);
        Http::fake([
            'slack.com/api/auth.teams.list' => Http::response([
                'ok' => true,
                'teams' => [
                    [
                        'id' => $teamId,
                        'name' => $teamName,
                    ],
                ],
            ]),
            sprintf('slack.com/api/users.info?user=%s', $userId) => Http::response([
                'ok' => false,
                'error' => 'not_authed',
            ]),
        ]);
        $this->actingAs($user)
            ->post(
                '/settings/link-slack',
                ['slack-team' => $teamId, 'slack-user' => $userId]
            )
            ->assertStatus(302)
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(
            'slack_links',
            [
                'character_id' => null,
                'slack_team' => $teamId,
                'team_name' => $teamName,
                'slack_user' => $userId,
                'user_name' => null,
                'user_id' => $user->id,
            ]
        );
    }

    /**
     * Test creating a Slack link if the Slack team isn't found, but the user
     * is.
     * @test
     */
    public function testCreateSlackLinkNoTeams(): void
    {
        $user = User::factory()->create();
        $teamId = Str::random(10);
        $userId = Str::random(10);
        $userName = Str::random(12);
        Http::fake([
            'slack.com/api/auth.teams.list' => Http::response([
                'ok' => false,
                'error' => 'not_authed',
            ]),
            sprintf('slack.com/api/users.info?user=%s', $userId) => Http::response([
                'ok' => true,
                'user' => [
                    'name' => $userName,
                ],
            ]),
        ]);
        $this->actingAs($user)
            ->post(
                '/settings/link-slack',
                ['slack-team' => $teamId, 'slack-user' => $userId]
            )
            ->assertStatus(302)
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(
            'slack_links',
            [
                'character_id' => null,
                'slack_team' => $teamId,
                'team_name' => null,
                'slack_user' => $userId,
                'user_name' => $userName,
                'user_id' => $user->id,
            ]
        );
    }

    /**
     * Test creating a Slack link if the Slack team isn't found among the
     * returned teams, but the user is found.
     * @test
     */
    public function testCreateSlackLinkNoTeamMatch(): void
    {
        $user = User::factory()->create();
        $teamId = Str::random(10);
        $userId = Str::random(10);
        $userName = Str::random(12);
        Http::fake([
            'slack.com/api/auth.teams.list' => Http::response([
                'ok' => true,
                'teams' => [
                    [
                        'id' => Str::random(9),
                        'name' => Str::random(20),
                    ],
                ],
            ]),
            sprintf('slack.com/api/users.info?user=%s', $userId) => Http::response([
                'ok' => true,
                'user' => [
                    'name' => $userName,
                ],
            ]),
        ]);
        $this->actingAs($user)
            ->post(
                '/settings/link-slack',
                ['slack-team' => $teamId, 'slack-user' => $userId]
            )
            ->assertStatus(302)
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(
            'slack_links',
            [
                'character_id' => null,
                'slack_team' => $teamId,
                'team_name' => null,
                'slack_user' => $userId,
                'user_name' => $userName,
                'user_id' => $user->id,
            ]
        );
    }

    /**
     * Test creating a Slack link.
     * @test
     */
    public function testCreateSlackLinkValidResponse(): void
    {
        $user = User::factory()->create();
        $teamId = Str::random(10);
        $teamName = Str::random(12);
        $userId = Str::random(10);
        $userName = Str::random(12);
        Http::fake([
            'slack.com/api/auth.teams.list' => Http::response([
                'ok' => true,
                'teams' => [
                    [
                        'id' => $teamId,
                        'name' => $teamName,
                    ],
                ],
            ]),
            sprintf('slack.com/api/users.info?user=%s', $userId) => Http::response([
                'ok' => true,
                'user' => [
                    'name' => $userName,
                ],
            ]),
        ]);
        $this->actingAs($user)
            ->post(
                '/settings/link-slack',
                ['slack-team' => $teamId, 'slack-user' => $userId]
            )
            ->assertStatus(302)
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas(
            'slack_links',
            [
                'character_id' => null,
                'slack_team' => $teamId,
                'team_name' => $teamName,
                'slack_user' => $userId,
                'user_name' => $userName,
                'user_id' => $user->id,
            ]
        );
    }
}
