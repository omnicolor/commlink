<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Http\Controllers;

use App\Events\RollEvent;
use App\Models\User;
use App\Models\WebChannel;
use Facades\App\Services\DiceService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Modules\Alien\Models\Character;
use Modules\Alien\Rolls\Skill;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function route;
use function sprintf;

#[Group('alien')]
#[Medium]
final class RollControllerTest extends TestCase
{
    public function testRollNoCharacter(): void
    {
        Event::fake();
        self::actingAs(User::factory()->create())
            ->postJson(
                route('alien.rolls.store'),
                [
                    'character' => '123',
                    'skill' => 'comtech',
                    'type' => 'skill',
                ],
            )
            ->assertNotFound();
        Event::assertNotDispatched(RollEvent::class);
    }

    public function testRollWithoutCampaign(): void
    {
        Event::fake();
        DiceService::shouldReceive('rollOne')
            ->times(6)
            ->with(6)
            ->andReturn(1, 2, 3, 4, 5, 6);

        $user = User::factory()->create();
        $character = Character::factory()->create([
            'owner' => $user->email->address,
            'skills' => [
                'close-combat' => 2,
            ],
            'strength' => 3,
            'stress' => 1,
        ]);
        self::actingAs($user)
            ->postJson(
                route('alien.rolls.store'),
                [
                    'character' => $character->id,
                    'skill' => 'close-combat',
                    'type' => 'skill',
                ],
            )
            ->assertOk()
            ->assertJson([
                'data' => [
                    'panic' => false,
                    'pushable' => true,
                    'rolls' => [1, 2, 3, 4, 5, 6],
                    'success' => true,
                    'text' => 'Rolled 1 success',
                    'title' => sprintf(
                        '%s succeeded with 6 dice for Close combat (2+3+1)',
                        $character->name,
                    ),
                ],
                'links' => [
                    'character' => route('alien.characters.show', $character->id),
                    'pushes' => [],
                ],
            ])
            ->assertJsonMissingPath('links.campaign');

        Event::assertDispatched(RollEvent::class);
    }

    public function testRollWithCampaign(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $character = Character::factory()->create([
            'campaign_id' => 123,
            'owner' => $user->email->address,
            'skills' => [
                'close-combat' => 2,
            ],
            'strength' => 3,
            'stress' => 1,
        ]);
        self::actingAs($user)
            ->postJson(
                route('alien.rolls.store'),
                [
                    'character' => $character->id,
                    'skill' => 'close-combat',
                    'type' => 'skill',
                ],
            )
            ->assertOk()
            ->assertJson([
                'links' => [
                    'campaign' => route('campaigns.show', 123),
                ],
            ]);

        Event::assertDispatched(RollEvent::class);
    }

    public function testShowRollNotInCache(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('alien.rolls.show', (string)Str::uuid()))
            ->assertNotFound();
    }

    public function testShowRollCharacterNotYours(): void
    {
        $character = Character::factory()->create([
            'skills' => [
                'close-combat' => 2,
            ],
            'strength' => 3,
            'stress' => 1,
        ]);
        $channel = new WebChannel();
        $channel->setCharacter($character);
        $roll = (new Skill('skill close-combat', (string)$character, $channel))
            ->forWeb();
        $roll['id'] = (string)Str::uuid();
        $roll['created_at'] = now()->toAtomString();
        $roll['character'] = $character->id;
        Cache::put('roll:' . $roll['id'], $roll, 10);
        self::actingAs(User::factory()->create())
            ->getJson(route('alien.rolls.show', $roll['id']))
            ->assertNotFound();
    }

    public function testShowRoll(): void
    {
        $user = User::factory()->create();
        $character = Character::factory()->create([
            'owner' => $user->email->address,
            'skills' => [
                'close-combat' => 2,
            ],
            'strength' => 3,
            'stress' => 1,
        ]);
        $channel = new WebChannel();
        $channel->setCharacter($character);
        $roll = (new Skill('skill close-combat', (string)$character, $channel))
            ->forWeb();
        $roll['id'] = (string)Str::uuid();
        $roll['created_at'] = now()->toAtomString();
        $roll['character'] = $character->id;
        Cache::put('roll:' . $roll['id'], $roll, 10);
        self::actingAs($user)
            ->getJson(route('alien.rolls.show', $roll['id']))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'panic',
                    'pushable',
                    'rolls',
                    'success',
                    'text',
                    'title',
                ],
                'links' => [
                    'character',
                    'self',
                    'pushes',
                ],
            ]);
    }
}
