<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\User;

/**
 * Tests for the ChatUser test.
 * @group models
 * @medium
 */
final class ChatUserTest extends \Tests\TestCase
{
    /**
     * Test getting the partial hash for verify a chat user.
     * @test
     */
    public function testGetVerificationAttribute(): void
    {
        $user = ChatUser::factory()->make([
            'server_id' => 'server-id',
            'remote_user_id' => 'Ud3adb33f',
            'user_id' => 13,
        ]);
        self::assertSame('7c8ab8b31389dff56531', $user->verification);
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
        // Clean up previous runs.
        ChatUser::where('remote_user_name', 'scopeSlackTest')->delete();

        self::assertEmpty(
            ChatUser::slack()
                ->where('remote_user_name', 'scopeSlackTest')
                ->get()
        );

        $user1 = ChatUser::factory()->create([
            'remote_user_name' => 'scopeSlackTest',
            'server_type' => ChatUser::TYPE_SLACK,
        ]);
        self::assertCount(
            1,
            ChatUser::slack()
                ->where('remote_user_name', 'scopeSlackTest')
                ->get()
        );

        $user2 = ChatUser::factory()->create([
            'remote_user_name' => 'scopeSlackTest',
            'server_type' => 'discord',
        ]);
        self::assertCount(
            1,
            ChatUser::slack()
                ->where('remote_user_name', 'scopeSlackTest')
                ->get()
        );

        $user3 = ChatUser::factory()->create([
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
     * Test limiting the scope to unverified users.
     * @test
     */
    public function testScopeUnverified(): void
    {
        // Clean up previous runs.
        ChatUser::where('remote_user_name', 'scopeUnverifiedTest')->delete();

        self::assertEmpty(
            ChatUser::unverified()
                ->where('remote_user_name', 'scopeUnverifiedTest')
                ->get()
        );

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
        // Clean up previous runs.
        ChatUser::where('remote_user_name', 'scopeVerifiedTest')->delete();

        self::assertEmpty(
            ChatUser::verified()
                ->where('remote_user_name', 'scopeVerifiedTest')
                ->get()
        );

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

    public function testGetChatCharacter(): void
    {
        $chatUser = ChatUser::factory()->create();
        $character = Character::factory()->create();
        $chatCharacter = ChatCharacter::factory()->create([
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        self::assertSame(
            $character->id,
            $chatUser->chatCharacter->getCharacter()->id,
        );
    }

    /**
     * Test getting the Commlink user attached to a ChatUser.
     * @test
     */
    public function testGetUser(): void
    {
        $user = User::factory()->create();
        $chatUser = ChatUser::factory()->make(['user_id' => $user->id]);
        self::assertSame($user->email, $chatUser->user->email);
    }
}
