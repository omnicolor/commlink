<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Shadowrun5E;

use App\Models\Shadowrun5E\Augmentation;

/**
 * Unit tests for Augmentation class.
 * @covers \App\Models\Shadowrun5E\Augmentation
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 */
final class AugmentationTest extends \Tests\TestCase
{
    /**
     * @var Augmentation Subject under test
     */
    private Augmentation $augmentation;

    /**
     * Set up subject under test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->augmentation = new Augmentation('cyberears-1');
    }

    /**
     * Test loading an invalid augmentation.
     * @test
     */
    public function testLoadInvalid(): void
    {
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Augmentation "invalid" is invalid');
        new Augmentation('invalid');
    }

    /**
     * Test that loading an augmentation sets the ID.
     * @test
     */
    public function testLoadSetsId(): void
    {
        self::assertSame('cyberears-1', $this->augmentation->id);
    }

    /**
     * Test that loading an augmentation sets the availability.
     * @test
     */
    public function testLoadSetsAvailability(): void
    {
        self::assertSame('3', $this->augmentation->availability);
    }

    /**
     * Test that loading an augmentation sets the cost.
     * @test
     */
    public function testLoadSetsCost(): void
    {
        self::assertSame(3000, $this->augmentation->cost);
    }

    /**
     * Test that loading an augmentation sets essence cost.
     * @test
     */
    public function testLoadSetsEssence(): void
    {
        self::assertSame(0.2, $this->augmentation->essence);
    }

    /**
     * Test that loading an augmentation sets it incompatabilities.
     * @test
     */
    public function testLoadSetsIncompatibilities(): void
    {
        $expected = [
            'cyberears-1',
            'cyberears-2',
            'cyberears-3',
            'cyberears-4',
        ];
        self::assertSame($expected, $this->augmentation->incompatibilities);
    }

    /**
     * Test that loading an augmentation sets the name.
     * @test
     */
    public function testLoadSetsName(): void
    {
        self::assertSame('Cyberears', $this->augmentation->name);
    }

    /**
     * Test the __toString method.
     * @test
     */
    public function testToString(): void
    {
        self::assertSame('Cyberears', (string)$this->augmentation);
    }

    /**
     * Data provider for cyberware grades.
     * @return array<int, array<int, float|string>> [grade, expected essence]
     */
    public function getCyberwareGradesAndEssence(): array
    {
        return [
            ['Standard', 1.0],
            ['Unknown', 1.0],
            ['Used', 1.25],
            ['Alpha', 0.8],
            ['Beta', 0.7],
            ['Delta', 0.5],
            ['Gamma', 1.0], // Not supported yet
            ['Omega', 1.0], // Not supported yet
        ];
    }

    /**
     * Test the different grades of cyberware.
     * @dataProvider getCyberwareGradesAndEssence
     * @param string $grade
     * @param float $expectedEssence
     * @test
     */
    public function testCyberwareStandardGrade(
        string $grade,
        float $expectedEssence
    ): void {
        $mod = new Augmentation('bone-lacing-aluminum', $grade);
        self::assertSame($expectedEssence, $mod->essence);
    }

    /**
     * Test build with an invalid augmentation.
     * @test
     */
    public function testBuildInvalid(): void
    {
        self::expectException(\RuntimeException::class);
        Augmentation::build(['id' => 'unknown']);
    }

    /**
     * Test build() with a valid, but plain augmentation.
     * @test
     */
    public function testBuild(): void
    {
        $aug = Augmentation::build(['id' => 'bone-lacing-aluminum']);
        self::assertSame('Bone Lacing', $aug->name);
        self::assertNull($aug->grade);
    }

    /**
     * Test build() with a modded augmentation.
     * @test
     */
    public function testBuildModded(): void
    {
        $array = [
            'id' => 'cyberears-1',
            'essence' => 0.5,
            'modifications' => ['damper'],
            'grade' => 'alpha',
        ];
        $aug = Augmentation::build($array);
        self::assertSame('Cyberears', $aug->name);
        self::assertSame('alpha', $aug->grade);
        self::assertSame(0.5, $aug->essence);
        self::assertCount(1, $aug->modifications);
    }

    /**
     * Test build() with a Skilljack.
     * @test
     */
    public function testBuildSkilljack(): void
    {
        $array = [
            'id' => 'skilljack-1',
            'softs' => ['soft-zero'],
            'active' => true,
        ];
        $aug = Augmentation::build($array);
        self::assertSame('Skilljack', $aug->name);
        self::assertNull($aug->grade);
        self::assertTrue($aug->active);
        self::assertSame(['soft-zero'], $aug->softs);
    }

    /**
     * Test getCost() on some unmodified standard grade augmentations.
     * @test
     */
    public function testGetCostSimple(): void
    {
        self::assertSame(3000, $this->augmentation->getCost());
    }

    /**
     * Test getCost() on some augmentations with modifications.
     * @test
     */
    public function testGetCostWithModifications(): void
    {
        $this->augmentation->modifications[] = new Augmentation('damper');
        self::assertSame(5250, $this->augmentation->getCost());
    }

    /**
     * Test getCost() on some augmentations with modifications and different
     * grade.
     * @test
     */
    public function testGetCostWithModificationsAndGrade(): void
    {
        $aug = new Augmentation('cyberears-1', 'Beta');
        $aug->modifications[] = new Augmentation('damper');
        self::assertSame(7875, $aug->getCost());
    }

    /**
     * Data provider for cyberware grades.
     * @return array<int, array<int, int|string>> [grade, expected cost]
     */
    public function getCyberwareGradesAndCost(): array
    {
        return [
            ['Standard', 4000],
            ['Unknown', 4000],
            ['Used', 3000],
            ['Alpha', 4800],
            ['Beta', 6000],
            ['Delta', 10000],
            ['Gamma', 4000], // Not supported yet
            ['Omega', 4000], // Not supported yet
        ];
    }

    /**
     * Test a modification that has a built-in modification.
     * @dataProvider getCyberwareGradesAndCost
     * @param string $grade
     * @param int $cost
     * @test
     */
    public function testModifiedModification(string $grade, int $cost): void
    {
        $aug = new Augmentation('cybereyes-1', $grade);
        self::assertNotEmpty($aug->modifications);
        self::assertInstanceOf(Augmentation::class, $aug->modifications[0]);
        // PHPStan thinks that AugmentationArray can hold nulls. It's wrong.
        // @phpstan-ignore-next-line
        self::assertNull($aug->modifications[0]->cost);
        self::assertSame($cost, $aug->getCost());
    }
}
