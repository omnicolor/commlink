<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Tests for the ChatUser test.
 * @group models
 * @medium
 */
final class ChatUserTest extends \Tests\TestCase
{
    use RefreshDatabase;

    /**
     * Test getting the partial hash for verify a chat user.
     * @test
     */
    public function testGetVerificationAttribute(): void
    {
        /** @var ChatUser */
        $user = ChatUser::factory()->make([
            'server_id' => 'server-id',
            'remote_user_id' => 'Ud3adb33f',
            'user_id' => 13,
        ]);
        self::assertSame('7c8ab8b31389dff56531', $user->verification);
        /** @var ChatUser */
        $user = ChatUser::factory()->make([
            'server_id' => 'server_id',
            'remote_user_id' => 'UFO',
            'user_id' => 42,
        ]);
        self::assertSame('7722ffabb29218436721', $user->verification);
    }

    /**
     * Test limiting the scope to Slack chat users.
     * @test
     */
    public function testScopeSlack(): void
    {
        self::assertEmpty(
            ChatUser::slack()
                ->where('remote_user_name', 'scopeSlackTest')
                ->get()
        );

        ChatUser::factory()->create([
            'remote_user_name' => 'scopeSlackTest',
            'server_type' => ChatUser::TYPE_SLACK,
        ]);
        self::assertCount(
            1,
            ChatUser::slack()
                ->where('remote_user_name', 'scopeSlackTest')
                ->get()
        );

        ChatUser::factory()->create([
            'remote_user_name' => 'scopeSlackTest',
            'server_type' => 'discord',
        ]);
        self::assertCount(
            1,
            ChatUser::slack()
                ->where('remote_user_name', 'scopeSlackTest')
                ->get()
        );

        ChatUser::factory()->create([
            'remote_user_name' => 'scopeSlackTest',
            'server_type' => ChatUser::TYPE_SLACK,
        ]);
        self::assertCount(
            2,
            ChatUser::slack()
                ->where('remote_user_name', 'scopeSlackTest')
                ->get()
        );
    }

    /**
     * Test limiting the scope to Discord chat users.
     * @test
     */
    public function testScopeDiscord(): void
    {
        self::assertEmpty(
            ChatUser::discord()
                ->where('remote_user_name', 'scopeDiscordTest')
                ->get()
        );

        ChatUser::factory()->create([
            'remote_user_name' => 'scopeDiscordTest',
            'server_type' => ChatUser::TYPE_DISCORD,
        ]);
        self::assertCount(
            1,
            ChatUser::discord()
                ->where('remote_user_name', 'scopeDiscordTest')
                ->get()
        );

        ChatUser::factory()->create([
            'remote_user_name' => 'scopeDiscordTest',
            'server_type' => ChatUser::TYPE_DISCORD,
        ]);
        self::assertCount(
            2,
            ChatUser::discord()
                ->where('remote_user_name', 'scopeDiscordTest')
                ->get()
        );

        // Slack user that shouldn't get picked up.
        ChatUser::factory()->create([
            'remote_user_name' => 'scopeDiscordTest',
            'server_type' => ChatUser::TYPE_SLACK,
        ]);
        self::assertCount(
            2,
            ChatUser::discord()
                ->where('remote_user_name', 'scopeDiscordTest')
                ->get()
        );
    }

    /**
     * Test limiting the scope to unverified users.
     * @test
     */
    public function testScopeUnverified(): void
    {
        self::assertEmpty(
            ChatUser::unverified()
                ->where('remote_user_name', 'scopeUnverifiedTest')
                ->get()
        );

        /** @var ChatUser */
        $user = ChatUser::factory()->create([
            'remote_user_name' => 'scopeUnverifiedTest',
            'verified' => false,
        ]);
        self::assertCount(
            1,
            ChatUser::unverified()
                ->where('remote_user_name', 'scopeUnverifiedTest')
                ->get()
        );
    }

    /**
     * Test limiting the scope to verified users.
     * @test
     */
    public function testScopeVerified(): void
    {
        self::assertEmpty(
            ChatUser::verified()
                ->where('remote_user_name', 'scopeVerifiedTest')
                ->get()
        );

        /** @var ChatUser */
        $user = ChatUser::factory()->create([
            'remote_user_name' => 'scopeVerifiedTest',
            'verified' => true,
        ]);
        self::assertCount(
            1,
            ChatUser::verified()
                ->where('remote_user_name', 'scopeVerifiedTest')
                ->get()
        );
    }

    /**
     * Test trying to load a chat character.
     * @test
     */
    public function testGetChatCharacter(): void
    {
        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create();
        /** @var Character */
        $character = Character::factory()->create();
        $chatCharacter = ChatCharacter::factory()->create([
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        self::assertSame(
            $character->id,
            // @phpstan-ignore-next-line
            $chatUser->chatCharacter->getCharacter()->id,
        );
    }

    /**
     * Test getting the Commlink user attached to a ChatUser.
     * @test
     */
    public function testGetUser(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var ChatUser */
        $chatUser = ChatUser::factory()->make(['user_id' => $user->id]);
        // @phpstan-ignore-next-line
        self::assertSame($user->email, $chatUser->user->email);
    }
}
