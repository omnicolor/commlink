<?php

declare(strict_types=1);

namespace Modules\Capers\Tests\Feature\Http\Controllers;

use App\Models\User;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('capers')]
#[Small]
final class GearControllerTest extends TestCase
{
    public function testIndex(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('capers.gear.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'name',
                        'cost',
                        'id',
                        'quantity',
                        'type',
                    ],
                ],
                'links' => [
                    'self',
                ],
            ]);
    }
}
