<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\Models;

use DomainException;
use Modules\Avatar\Enums\Background;
use Modules\Avatar\Enums\Condition;
use Modules\Avatar\Enums\TechniqueLevel;
use Modules\Avatar\Enums\Training;
use Modules\Avatar\Features\TheLodestar;
use Modules\Avatar\Models\Character;
use Modules\Avatar\Models\Move;
use Modules\Avatar\Models\Playbook;
use Modules\Avatar\Models\Status;
use Modules\Avatar\Models\Technique;
use Modules\Avatar\ValueObjects\GrowthAdvancements;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('avatar')]
#[Small]
final class CharacterTest extends TestCase
{
    public function testToStringUnnamed(): void
    {
        $character = new Character();
        self::assertSame('Unnamed character', (string)$character);
    }

    public function testToStringNamed(): void
    {
        $character = new Character(['name' => 'Aang']);
        self::assertSame('Aang', (string)$character);
    }

    public function testSetBackgroundEnum(): void
    {
        $character = new Character();
        $character->background = Background::Urban;
        self::assertSame('urban', $character->background->value);
    }

    public function testSetBackgroundString(): void
    {
        $character = new Character(['background' => 'outlaw']);
        self::assertSame(Background::Outlaw, $character->background);
    }

    public function testEmptyConditions(): void
    {
        $character = new Character();
        self::assertSame([], $character->conditions);
    }

    public function testSetConditionsConstructor(): void
    {
        $character = new Character([
            'conditions' => [
                'afraid',
                'angry',
            ],
        ]);
        self::assertCount(2, $character->conditions);
    }

    public function testSetConditions(): void
    {
        $character = new Character();
        $character->conditions = [
            'angry',
            Condition::Guilty,
        ];
        self::assertCount(2, $character->conditions);
    }

    public function testNoFatigue(): void
    {
        $character = new Character();
        self::assertSame(0, $character->fatigue);
    }

    public function testFatigueConstructor(): void
    {
        $character = new Character(['fatigue' => 1]);
        self::assertSame(1, $character->fatigue);
    }

    public function testSetFatigue(): void
    {
        $character = new Character();
        $character->fatigue = 2;
        self::assertSame(2, $character->fatigue);
    }

    public function testGrowthAdvancementDefault(): void
    {
        $character = new Character();
        self::assertEquals(
            new GrowthAdvancements([]),
            $character->growth_advancements,
        );
    }

    public function testGrowthAdvancements(): void
    {
        $character = new Character([
            'growth_advancements' => [
                'new_move_from_my_playbook' => 1,
                'new_move_from_another_playbook' => 2,
                'shift_your_center' => 1,
                'unlock_your_moment_of_balance' => 2,
            ],
        ]);
        $advancement = $character->growth_advancements;

        self::assertSame(1, $advancement->new_move_from_my_playbook);
        self::assertSame(2, $advancement->new_move_from_another_playbook);
        self::assertSame(1, $advancement->shift_your_center);
        self::assertSame(2, $advancement->unlock_your_moment_of_balance);
    }

    public function testSetPlaybookConstructor(): void
    {
        $character = new Character(['playbook' => 'the-adamant']);
        self::assertSame('The Adamant', (string)$character->playbook);
        $feature = $character->playbook->feature;
        self::assertInstanceOf(TheLodestar::class, $feature);
        self::assertSame('Unknown', $feature->lodestar);
    }

    public function testPlaybookWithConstructor(): void
    {
        $character = new Character([
            'playbook' => 'the-adamant',
            'playbook_options' => [
                'lodestar' => 'Phil',
            ],
        ]);
        $feature = $character->playbook->feature;
        self::assertInstanceOf(TheLodestar::class, $feature);
        self::assertSame('Phil', $feature->lodestar);
    }

    public function testSetPlaybook(): void
    {
        $playbook = new Playbook('the-adamant');
        $character = new Character();
        $character->playbook = $playbook;

        self::assertSame(166, $character->playbook->page);
    }

    public function testStatsNoModifiers(): void
    {
        $character = new Character(['playbook' => 'the-adamant']);
        self::assertSame(0, $character->creativity);
        self::assertSame(1, $character->focus);
        self::assertSame(-1, $character->harmony);
        self::assertSame(1, $character->passion);
    }

    public function testStatsWithModifiers(): void
    {
        $character = new Character([
            'creativity' => 1,
            'focus' => 1,
            'harmony' => 1,
            'passion' => 1,
            'playbook' => 'the-adamant',
        ]);
        self::assertSame(1, $character->creativity);
        self::assertSame(2, $character->focus);
        self::assertSame(0, $character->harmony);
        self::assertSame(2, $character->passion);
    }

    public function testMovesEmpty(): void
    {
        $character = new Character();
        self::assertCount(0, $character->moves);
    }

    public function testMoves(): void
    {
        $character = new Character(['moves' => ['this-was-a-victory']]);
        self::assertCount(1, $character->moves);
        self::assertSame('This Was a Victory', (string)$character->moves[0]);
    }

    public function testSetMoves(): void
    {
        $character = new Character();
        $character->moves = [
            'no-time-for-feelings',
            new Move('this-was-a-victory'),
        ];
        self::assertCount(2, $character->moves);
    }

    public function testStatusesEmpty(): void
    {
        $character = new Character();
        self::assertCount(0, $character->statuses);
    }

    public function testStatusesConstructor(): void
    {
        $character = new Character(['statuses' => ['empowered']]);
        self::assertCount(1, $character->statuses);
        self::assertStringStartsWith(
            'Empowered is the status for when a Waterbender',
            $character->statuses[0]->description,
        );
    }

    public function testSetStatuses(): void
    {
        $character = new Character();
        $status = new Status('doomed');
        $character->statuses = [$status];
        self::assertCount(1, $character->statuses);
    }

    public function testTechniquesEmpty(): void
    {
        $character = new Character();
        self::assertCount(0, $character->techniques);
    }

    public function testTechniquesSetInConstructor(): void
    {
        $character = new Character([
            'techniques' => [
                ['id' => 'a-single-spark', 'level' => 'learned'],
            ],
        ]);
        self::assertCount(1, $character->techniques);
        self::assertInstanceOf(Technique::class, $character->techniques[0]);
        self::assertSame('A Single Spark', (string)$character->techniques[0]);
    }

    public function testSetTechnique(): void
    {
        $character = new Character();
        $technique = Technique::findOrFail('blood-twisting');
        $technique->level = TechniqueLevel::Practiced;
        $character->techniques = [$technique];
        self::assertCount(1, $character->techniques);
    }

    public function testTrainingNotSet(): void
    {
        self::assertNull((new Character())->training);
    }

    public function testTrainingSetInConstructor(): void
    {
        $character = new Character(['training' => 'Airbending']);
        self::assertSame(Training::Airbending, $character->training);
    }

    public function testSetTrainingString(): void
    {
        $character = new Character();
        $character->training = 'Earthbending';
        self::assertSame(Training::Earthbending, $character->training);
    }

    public function testSetTrainingEnum(): void
    {
        $character = new Character();
        $character->training = Training::Firebending;
        self::assertSame(Training::Firebending, $character->training);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public static function attributeProvider(): array
    {
        return [
            ['creativity'],
            ['focus'],
            ['harmony'],
            ['passion'],
        ];
    }

    #[DataProvider('attributeProvider')]
    public function testAttributesCanNotBeTooLow(string $attribute): void
    {
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Attributes can not be less than -1');
        $character = new Character([
            $attribute => -2,
            'playbook' => 'the-adamant',
        ]);
        // @phpstan-ignore property.dynamicName, expr.resultUnused
        $character->$attribute;
    }

    #[DataProvider('attributeProvider')]
    public function testAttributesCanNotBeToohigh(string $attribute): void
    {
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Attributes can not be greater than 4');
        $character = new Character([$attribute => 5]);
        // @phpstan-ignore property.dynamicName, expr.resultUnused
        $character->$attribute;
    }
}
