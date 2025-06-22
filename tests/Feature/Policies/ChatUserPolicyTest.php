<?php

declare(strict_types=1);

namespace Tests\Feature\Policies;

use App\Models\ChatUser;
use App\Models\User;
use App\Policies\ChatUserPolicy;
use Override;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Medium]
final class ChatUserPolicyTest extends TestCase
{
    private ChatUserPolicy $policy;
    private User $owner;
    private User $other_user;
    private ChatUser $chat_user;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        if (isset($this->policy)) {
            return;
        }
        $this->policy = new ChatUserPolicy();
        $this->owner = User::factory()->create();
        $this->other_user = User::factory()->create();
        $this->chat_user = ChatUser::factory()->create([
            'user_id' => $this->owner->id,
        ]);
    }

    public function testCanNotViewAny(): void
    {
        self::assertFalse($this->policy->viewAny($this->owner));
        self::assertFalse($this->policy->viewAny($this->other_user));
    }

    public function testOwnerCanView(): void
    {
        self::assertTrue($this->policy->view($this->owner, $this->chat_user));
    }

    public function testOtherUserCanNotView(): void
    {
        self::assertFalse(
            $this->policy->view($this->other_user, $this->chat_user),
        );
    }

    public function testCanCreate(): void
    {
        self::assertTrue($this->policy->create($this->owner));
        self::assertTrue($this->policy->create($this->other_user));
    }

    public function testOwnerCanUpdate(): void
    {
        self::assertTrue($this->policy->update($this->owner, $this->chat_user));
    }

    public function testOtherUserCanNotUpdate(): void
    {
        self::assertFalse(
            $this->policy->update($this->other_user, $this->chat_user),
        );
    }

    public function testOwnerCanDelete(): void
    {
        self::assertTrue($this->policy->delete($this->owner, $this->chat_user));
    }

    public function testOtherUserCanNotDelete(): void
    {
        self::assertFalse(
            $this->policy->delete($this->other_user, $this->chat_user),
        );
    }

    public function testNoOneCanRestore(): void
    {
        self::assertFalse($this->policy->restore());
        self::assertFalse($this->policy->restore());
    }

    public function testNoOneCanForceDelete(): void
    {
        self::assertFalse($this->policy->forceDelete());
        self::assertFalse($this->policy->forceDelete());
    }
}
