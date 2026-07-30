<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\ValueObjects;

use Illuminate\Foundation\Testing\WithFaker;
use Modules\Shadowrun6e\ValueObjects\KnowledgeSkill;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class KnowledgeSkillTest extends TestCase
{
    use WithFaker;

    public function testToString(): void
    {
        $name = $this->faker->name;
        $skill = new KnowledgeSkill($name);
        self::assertSame($name, (string)$skill);
    }
}
