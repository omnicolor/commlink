<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Tests\Feature\Models;

use Modules\Dnd5e\Enums\Ability;
use Modules\Dnd5e\Models\Role;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('dnd5e')]
#[Small]
final class RoleTest extends TestCase
{
    public function testToString(): void
    {
        $role = Role::findOrFail('barbarian');
        self::assertSame('Barbarian', (string)$role);
    }

    public function testPrimaryAbility(): void
    {
        $role = Role::findOrFail('barbarian');
        self::assertSame(Ability::Strength, $role->primary_ability);
    }

    public function testSavingThrowProficiencies(): void
    {
        $role = Role::findOrFail('barbarian');
        self::assertSame(
            [
                Ability::Strength,
                Ability::Constitution,
            ],
            $role->saving_throw_proficiencies,
        );
    }
}
