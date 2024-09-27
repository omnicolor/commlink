<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\User;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

/**
 * Tests for the ChatUser test.
 */
#[Medium]
final class ChatUserTest extends TestCase
{
    /**
     * Test getting the verification code for a user.
     */
    public function testGetVerificationAttribute(): void
    {
        /** @var ChatUser */
        $user = ChatUser::factory()->make([
            'server_id' => 'server-id',
            'remote_user_id' => 'Ud3adb33f',
            'user_id' => 13,
        ]);
        self::assertSame(
            \substr(\sha1(config('app.key') . 'server-idUd3adb33f13'), 0, 20),
            $user->verification
        );
    }

    /**
     * Test limiting the scope to Slack chat users.
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
     */
    public function testGetChatCharacter(): void
    {
        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create();
        /** @var Character */
        $character = Character::factory()->create([
            'created_by' => self::class . '::' . __FUNCTION__,
        ]);
        $chatCharacter = ChatCharacter::factory()->create([
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        self::assertSame(
            $character->id,
            $chatUser->chatCharacter?->getCharacter()?->id,
        );
        $character->delete();
    }

    /**
     * Test getting the Commlink user attached to a ChatUser.
     */
    public function testGetUser(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var ChatUser */
        $chatUser = ChatUser::factory()->make(['user_id' => $user->id]);
        self::assertSame($user->email, $chatUser->user->email);
    }
}
