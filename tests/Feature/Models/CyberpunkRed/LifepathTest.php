<?php

declare(strict_types=1);

namespace Tests\Feature\Models\CyberpunkRed;

use App\Models\CyberpunkRed\Lifepath;
use RuntimeException;

/**
 * Unit tests for CyberpunkRed lifepath.
 * @covers \App\Models\CyberpunkRed\Lifepath
 * @group cyberpunkred
 * @group models
 * @small
 */
final class LifepathTest extends \Tests\TestCase
{
    public function testGetAffectationNoValue(): void
    {
        self::expectException(RuntimeException::class);
        (new Lifepath([]))->getAffectation();
    }

    public function testGetAffectation(): void
    {
        $lifepath = new Lifepath(['affectation' => ['chosen' => 1]]);
        self::assertSame('Tattoos', $lifepath->getAffectation());
    }

    public function testGetBackgroundNoValue(): void
    {
        self::expectException(RuntimeException::class);
        (new Lifepath([]))->getBackground();
    }

    public function testGetBackground(): void
    {
        $expected = [
            'name' => 'Corporate managers',
            'description' => 'Well to do, with large homes, safe '
                . 'neighborhoods, nice cars, etc. Sometimes your parent(s) '
                . 'would hire servants, although this was rare. You had a mix '
                . 'of private and corporate education.',
        ];
        $lifepath = new Lifepath(['background' => ['chosen' => 2]]);
        self::assertSame($expected, $lifepath->getBackground());
    }

    public function testGetClothingNoValue(): void
    {
        self::expectException(RuntimeException::class);
        (new Lifepath([]))->getClothing();
    }

    public function testGetClothing(): void
    {
        $lifepath = new Lifepath(['clothing' => ['chosen' => 3]]);
        self::assertSame(
            'Urban Flash (Flashy, Technological, Streetwear)',
            $lifepath->getClothing()
        );
    }

    public function testGetEnvironmentsNoValue(): void
    {
        self::expectException(RuntimeException::class);
        (new Lifepath([]))->getEnvironment();
    }

    public function testGetEnvironments(): void
    {
        $lifepath = new Lifepath(['environment' => ['chosen' => 4]]);
        self::assertSame(
            'In a Nomad pack with roots in transport (ships, planes, caravans).',
            $lifepath->getEnvironment()
        );
    }

    public function testGetFamilyCrisisNoValue(): void
    {
        self::expectException(RuntimeException::class);
        (new Lifepath([]))->getFamilyCrisis();
    }

    public function testGetFamilyCrisis(): void
    {
        $lifepath = new Lifepath(['family-crisis' => ['chosen' => 5]]);
        self::assertSame(
            'Your family vanished. You are the only remaining member.',
            $lifepath->getFamilyCrisis()
        );
    }

    public function testGetFeelingNoValue(): void
    {
        self::expectException(RuntimeException::class);
        (new Lifepath([]))->getFeeling();
    }

    public function testGetFeeling(): void
    {
        $lifepath = new Lifepath(['feeling' => ['chosen' => 6]]);
        self::assertSame(
            'Every person is a valuable individual.',
            $lifepath->getFeeling()
        );
    }

    public function testGetHairStyleNoValue(): void
    {
        self::expectException(RuntimeException::class);
        (new Lifepath([]))->getHairStyle();
    }

    public function testGetHairStyle(): void
    {
        $lifepath = new Lifepath(['hair' => ['chosen' => 7]]);
        self::assertSame('Wild colors', $lifepath->getHairStyle());
    }

    public function testGetOriginNoValue(): void
    {
        self::expectException(RuntimeException::class);
        (new Lifepath([]))->getOrigin();
    }

    public function testGetOrigin(): void
    {
        $expected = [
            'name' => 'South East Asian',
            'languages' => [
                'Arabic', 'Burmese', 'English', 'Filipino', 'Hindi', 'Indonesian', 'Khmer', 'Malayan', 'Vietnamese',
            ],
        ];
        $lifepath = new Lifepath(['origin' => ['chosen' => 8]]);
        self::assertSame($expected, $lifepath->getOrigin());
    }

    public function testGetPersonalityNoValue(): void
    {
        self::expectException(RuntimeException::class);
        (new Lifepath([]))->getPersonality();
    }

    public function testGetPersonality(): void
    {
        $lifepath = new Lifepath(['personality' => ['chosen' => 9]]);
        self::assertSame(
            'Intellectual and detached',
            $lifepath->getPersonality()
        );
    }

    public function testGetPersonNoValue(): void
    {
        self::expectException(RuntimeException::class);
        (new Lifepath([]))->getPerson();
    }

    public function testGetPerson(): void
    {
        $lifepath = new Lifepath(['person' => ['chosen' => 10]]);
        self::assertSame('No one', $lifepath->getPerson());
    }

    public function testGetPossessionNoValue(): void
    {
        self::expectException(RuntimeException::class);
        (new Lifepath([]))->getPossession();
    }

    public function testGetPossession(): void
    {
        $lifepath = new Lifepath(['possession' => ['chosen' => 1]]);
        self::assertSame('A weapon', $lifepath->getPossession());
    }

    public function testGetValuesNoValue(): void
    {
        self::expectException(RuntimeException::class);
        (new Lifepath([]))->getValues();
    }

    public function testGetValues(): void
    {
        $lifepath = new Lifepath(['value' => ['chosen' => 2]]);
        self::assertSame('Honor', $lifepath->getValues());
    }
}
