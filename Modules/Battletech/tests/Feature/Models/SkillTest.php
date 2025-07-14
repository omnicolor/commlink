<?php

declare(strict_types=1);

namespace Modules\Battletech\Tests\Feature\Models;

use DomainException;
use Modules\Battletech\Enums\ActionRating;
use Modules\Battletech\Enums\Attribute;
use Modules\Battletech\Enums\TrainingRating;
use Modules\Battletech\Models\Character;
use Modules\Battletech\Models\Skill;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('battletech')]
#[Small]
final class SkillTest extends TestCase
{
    private Skill $acting;
    private Skill $acrobatics;
    private Skill $administration;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        if (isset($this->acting)) {
            return;
        }

        $this->acrobatics = Skill::findOrFail('acrobatics-free-fall');
        $this->acting = Skill::findOrFail('acting');
        $this->administration = Skill::findOrFail('administration');
    }

    public function testToString(): void
    {
        self::assertSame('Acting', (string)$this->acting);
        self::assertSame('Acrobatics/Free-Fall', (string)$this->acrobatics);
        $acting = clone $this->acting;
        $acting->specialty = 'Seduction';
        self::assertSame('Acting (Seduction)', (string)$acting);
        $acrobatics = clone $this->acrobatics;
        $acrobatics->specialty = 'Flips';
        self::assertSame('Acrobatics/Free-Fall (Flips)', (string)$acrobatics);
    }

    public function testAttributes(): void
    {
        self::assertSame([Attribute::Charisma], $this->acting->attributes);
        self::assertSame(
            [Attribute::Intelligence, Attribute::Willpower],
            $this->administration->attributes,
        );
    }

    public function testActionRating(): void
    {
        self::assertSame(
            ActionRating::Simple,
            $this->acrobatics->action_rating,
        );
        self::assertSame(
            ActionRating::Complex,
            $this->acting->action_rating,
        );
    }

    public function testTrainingRating(): void
    {
        self::assertSame(
            TrainingRating::Basic,
            $this->acting->training_rating,
        );
        self::assertSame(
            TrainingRating::Advanced,
            $this->administration->training_rating,
        );
    }

    /**
     * @return array<int, array<int, int|null>>
     */
    public static function skillLevelCostProvider(): array
    {
        return [
            [null, 20, 16, 24],
            [0, 10, 8, 12],
            [1, 20, 16, 24],
            [9, 100, 80, 120],
        ];
    }

    #[DataProvider('skillLevelCostProvider')]
    public function testCostToRaise(
        int|null $current_level,
        int $standard_cost,
        int $fast_cost,
        int $slow_cost,
    ): void {
        $skill = clone $this->acting;
        $skill->level = $current_level;

        $normal = new Character();
        $fast = new Character(['traits' => ['fast-learner']]);
        $slow = new Character(['traits' => ['slow-learner']]);

        self::assertSame($standard_cost, $skill->getCostToRaise($normal));
        self::assertSame($fast_cost, $skill->getCostToRaise($fast));
        self::assertSame($slow_cost, $skill->getCostToRaise($slow));
    }

    public function testCostToRaisePastLimit(): void
    {
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Skills can not be raised past level 10');
        $skill = clone $this->acting;
        $skill->level = 10;
        $skill->getCostToRaise(new Character());
    }
}
