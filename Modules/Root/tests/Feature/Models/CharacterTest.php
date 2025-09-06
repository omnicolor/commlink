<?php

declare(strict_types=1);

namespace Modules\Root\Tests\Feature\Models;

use DomainException;
use Iterator;
use Modules\Root\Models\Character;
use Modules\Root\Models\Move;
use Modules\Root\Models\Nature;
use Modules\Root\Models\Playbook;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('root')]
#[Small]
final class CharacterTest extends TestCase
{
    public function testToStringNotSet(): void
    {
        $character = new Character();
        self::assertSame('Unnamed character', (string)$character);
    }

    public function testToString(): void
    {
        $character = new Character(['name' => 'Floppy the Fierce']);
        self::assertSame('Floppy the Fierce', (string)$character);
    }

    /**
     * @return Iterator<int, array<int, string>>
     */
    public static function attributeProvider(): Iterator
    {
        yield ['charm'];
        yield ['cunning'];
        yield ['finesse'];
        yield ['luck'];
        yield ['might'];
    }

    #[DataProvider('attributeProvider')]
    public function testAttributesCanNotBeTooLow(string $attribute): void
    {
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Attributes can not be less than -1');
        $character = new Character([$attribute => -2]);
        // @phpstan-ignore property.dynamicName, expr.resultUnused
        $character->$attribute;
    }

    #[DataProvider('attributeProvider')]
    public function testAttributesCanNotBeToohigh(string $attribute): void
    {
        self::expectException(DomainException::class);
        self::expectExceptionMessage('Attributes can not be greater than 2');
        $character = new Character([$attribute => 3]);
        // @phpstan-ignore property.dynamicName, expr.resultUnused
        $character->$attribute;
    }

    public function testNoMoves(): void
    {
        $character = new Character();
        self::assertCount(0, $character->moves);
    }

    public function testNewWithMoves(): void
    {
        $character = new Character([
            'might' => 1,
            'moves' => ['brute', 'carry-a-big-stick'],
        ]);
        self::assertCount(2, $character->moves);
        self::assertEquals(Move::find('brute'), $character->moves->first());
        self::assertSame(2, $character->might->value);
    }

    public function testNoNature(): void
    {
        $character = new Character();
        self::assertNull($character->nature);
    }

    public function testNewWithNature(): void
    {
        $character = new Character(['nature' => 'defender']);
        self::assertEquals(Nature::find('defender'), $character->nature);
    }

    public function testSetNatureObject(): void
    {
        $character = new Character();
        $character->nature = Nature::findOrFail('defender');
        self::assertSame('Defender', (string)$character->nature);
    }

    public function testSetNatureString(): void
    {
        $character = new Character();
        $character->nature = 'punisher';
        self::assertEquals(Nature::find('punisher'), $character->nature);
    }

    public function testNoPlaybook(): void
    {
        $character = new Character();
        self::assertNull($character->playbook);
    }

    public function testNewWithPlaybook(): void
    {
        $character = new Character(['playbook' => 'arbiter']);
        self::assertEquals(Playbook::find('arbiter'), $character->playbook);
    }

    public function testSetPlaybookObject(): void
    {
        $character = new Character();
        $character->playbook = Playbook::findOrFail('arbiter');
        self::assertSame('The Arbiter', (string)$character->playbook);
    }

    public function testSetPlaybookString(): void
    {
        $character = new Character();
        $character->playbook = 'arbiter';
        self::assertSame('The Arbiter', (string)$character->playbook);
    }

    public function testDefaultTracks(): void
    {
        $character = new Character();
        self::assertSame(4, $character->decay_max);
        self::assertSame(0, $character->decay);
        self::assertSame(4, $character->exhaustion_max);
        self::assertSame(0, $character->exhaustion);
        self::assertSame(4, $character->injury_max);
        self::assertSame(0, $character->injury);
    }

    public function testTracksPartiallyFilled(): void
    {
        $character = new Character([
            'decay' => 1,
            'exhaustion' => 2,
            'injury' => 3,
        ]);
        self::assertSame(1, $character->decay);
        self::assertSame(2, $character->exhaustion);
        self::assertSame(3, $character->injury);
    }
}
