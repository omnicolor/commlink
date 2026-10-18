<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Ruleset;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

#[CoversClass(Ruleset::class)]
#[Medium]
final class RulesetTest extends TestCase
{
    #[Test]
    #[TestDox('Ruleset can be scoped to a system module')]
    public function testScopeModule(): void
    {
        $rulesets = Ruleset::module('avatar')->get();
        self::assertCount(1, $rulesets);
        $rulesets = Ruleset::module('alien')->get();
        self::assertCount(2, $rulesets);
    }

    #[Test]
    #[TestDox('Ruleset returns zero results when scoping to an invalid module')]
    public function testScopeModuleNotFound(): void
    {
        $rulesets = Ruleset::module('unknown')->get();
        self::assertCount(0, $rulesets);
    }

    #[Test]
    #[TestDox('Ruleset can be scoped to only required books within a module')]
    public function testScopeRequired(): void
    {
        $rulesets = Ruleset::module('alien')
            ->required()
            ->get();
        self::assertCount(1, $rulesets);
    }

    #[Test]
    #[TestDox('Casting a ruleset to a string returns its name')]
    public function testToString(): void
    {
        $ruleset = Ruleset::findOrFail('alien-core');
        self::assertSame('Core Rulebook', (string)$ruleset);
    }
}
