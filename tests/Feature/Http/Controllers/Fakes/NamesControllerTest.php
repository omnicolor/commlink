<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Fakes;

use App\Models\User;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Small]
final class NamesControllerTest extends TestCase
{
    public function testBasic(): void
    {
        self::actingAs(User::factory()->create())
            ->get(route('fakes.names'))
            ->assertOk()
            ->assertJsonCount(5, 'data');
    }

    public function testWithQueryParam(): void
    {
        self::actingAs(User::factory()->create())
            ->get(route('fakes.names', ['quantity' => 10]))
            ->assertOk()
            ->assertJsonCount(10, 'data');
    }
}
