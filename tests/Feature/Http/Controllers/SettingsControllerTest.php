<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\SlackLink;
use App\Models\User;
use Illuminate\Http\Response;
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
                '%s — %s',
                $slackLink->slack_team,
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
     * Test creating a Slack link.
     * @test
     */
    public function testCreateSlackLink(): void
    {
        $user = User::factory()->create();
        $teamId = Str::random(10);
        $userId = Str::random(10);
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
                'slack_user' => $userId,
                'user_id' => $user->id,
            ]
        );
    }
}
