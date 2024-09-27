<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Small]
final class PulseControllerTest extends TestCase
{
    public function testNotLoggedIn(): void
    {
        self::get(route('pulse'))->assertForbidden();
    }

    public function testNotAdmin(): void
    {
        self::actingAs(User::factory()->create())
            ->get(route('pulse'))
            ->assertForbidden();
    }

    public function testAdmin(): void
    {
        $user = User::factory()->admin()->create();
        self::actingAs($user)->get(route('pulse'))->assertOk();
    }
}
