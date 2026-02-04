<?php

declare(strict_types=1);

namespace Modules\Battletech\Tests\Feature\Http\Controllers;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

#[Group('battletech')]
#[Medium]
final class SkillsControllerTest extends TestCase
{
    private User $user;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        if (isset($this->user)) {
            return;
        }
        $this->seed(RoleAndPermissionSeeder::class);
        $this->app->make(PermissionRegistrar::class)
            ->forgetCachedPermissions();
        $this->user = User::factory()->create();
        $this->user->assignRole('trusted');
    }

    public function testIndex(): void
    {
        self::actingAs($this->user)
            ->getJson(route('battletech.skills.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'action_rating',
                        'attributes',
                        'description',
                        'id',
                        'name',
                        'page',
                        'quote',
                        'ruleset',
                        'target_number',
                        'training_rating',
                        'links',
                    ],
                ],
            ]);
    }

    public function testShow(): void
    {
        self::actingAs($this->user)
            ->getJson(route('battletech.skills.show', 'acting'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'action_rating',
                    'attributes',
                    'description',
                    'id',
                    'name',
                    'page',
                    'quote',
                    'ruleset',
                    'sub_description',
                    'sub_name',
                    'target_number',
                    'training_rating',
                    'links',
                ],
            ]);
    }
}
