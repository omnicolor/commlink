<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Ruleset;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Medium]
final class RulesetTest extends TestCase
{
    public function testScopeModule(): void
    {
        $rulesets = Ruleset::module('avatar')->get();
        self::assertCount(1, $rulesets);
        $rulesets = Ruleset::module('alien')->get();
        self::assertCount(2, $rulesets);
    }

    public function testScopeModuleNotFound(): void
    {
        $rulesets = Ruleset::module('unknown')->get();
        self::assertCount(0, $rulesets);
    }

    public function testScopeRequired(): void
    {
        $rulesets = Ruleset::module('alien')
            ->required()
            ->get();
        self::assertCount(1, $rulesets);
    }

    public function testToString(): void
    {
        $ruleset = Ruleset::findOrFail('alien-core');
        self::assertSame('Core Rulebook', (string)$ruleset);
    }
}
