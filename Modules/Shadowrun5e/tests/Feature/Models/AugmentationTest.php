<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Enums\AugmentationGrade;
use Modules\Shadowrun5e\Enums\AugmentationType;
use Modules\Shadowrun5e\Models\Augmentation;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class AugmentationTest extends TestCase
{
    private Augmentation $augmentation;

    /**
     * Set up subject under test.
     */
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->augmentation = new Augmentation('cyberears-1');
    }

    /**
     * Test loading an invalid augmentation.
     */
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Augmentation "invalid" is invalid');
        new Augmentation('invalid');
    }

    /**
     * Test that loading an augmentation sets the ID.
     */
    public function testLoadSetsId(): void
    {
        self::assertSame('cyberears-1', $this->augmentation->id);
    }

    /**
     * Test that loading an augmentation sets the availability.
     */
    public function testLoadSetsAvailability(): void
    {
        self::assertSame('3', $this->augmentation->availability);
    }

    /**
     * Test that loading an augmentation sets the cost.
     */
    public function testLoadSetsCost(): void
    {
        self::assertSame(3000, $this->augmentation->cost);
    }

    /**
     * Test that loading an augmentation sets essence cost.
     */
    public function testLoadSetsEssence(): void
    {
        self::assertSame(0.2, $this->augmentation->essence);
    }

    /**
     * Test that loading an augmentation sets its incompatabilities.
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
     */
    public function testLoadSetsName(): void
    {
        self::assertSame('Cyberears', $this->augmentation->name);
    }

    /**
     * Test that loading an augmentation sets the type of augmentation.
     */
    public function testLoadSetsType(): void
    {
        self::assertSame(AugmentationType::Cyberware, $this->augmentation->type);

        $augmentation = new Augmentation('bone-density-augmentation-2');
        self::assertSame(AugmentationType::Bioware, $augmentation->type);
    }

    /**
     * Test the __toString method.
     */
    public function testToString(): void
    {
        self::assertSame('Cyberears', (string)$this->augmentation);
    }

    /**
     * Data provider for cyberware grades.
     * @return array<int, array{0: AugmentationGrade|string, 1: float}> [grade, expected essence]
     */
    public static function cyberwareGradesAndEssenceProvider(): array
    {
        return [
            [AugmentationGrade::Standard, 1.0],
            ['Unknown', 1.0],
            [AugmentationGrade::Used, 1.25],
            [AugmentationGrade::Alpha, 0.8],
            [AugmentationGrade::Beta, 0.7],
            [AugmentationGrade::Delta, 0.5],
            [AugmentationGrade::Gamma, 1.0], // Not supported yet
            [AugmentationGrade::Omega, 1.0], // Not supported yet
        ];
    }

    /**
     * Test the different grades of cyberware.
     */
    #[DataProvider('cyberwareGradesAndEssenceProvider')]
    public function testCyberwareStandardGrade(
        AugmentationGrade|string $grade,
        float $expectedEssence,
    ): void {
        $mod = new Augmentation('bone-lacing-aluminum', $grade);
        self::assertSame($expectedEssence, $mod->essence);
    }

    /**
     * Test build with an invalid augmentation.
     */
    public function testBuildInvalid(): void
    {
        self::expectException(RuntimeException::class);
        Augmentation::build(['id' => 'unknown']);
    }

    /**
     * Test build() with a valid, but plain augmentation.
     */
    public function testBuild(): void
    {
        $aug = Augmentation::build(['id' => 'bone-lacing-aluminum']);
        self::assertSame('Bone Lacing', $aug->name);
        self::assertEquals(AugmentationGrade::Standard, $aug->grade);
    }

    /**
     * Test build() with a modded augmentation.
     */
    public function testBuildModded(): void
    {
        $array = [
            'id' => 'cyberears-1',
            'essence' => 0.5,
            'modifications' => ['damper'],
            'grade' => 'Alpha',
        ];
        $aug = Augmentation::build($array);
        self::assertSame('Cyberears', $aug->name);
        self::assertSame('Alpha', $aug->grade->value);
        self::assertSame(0.5, $aug->essence);
        self::assertCount(1, $aug->modifications);
    }

    /**
     * Test build() with a Skilljack.
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
        self::assertEquals(AugmentationGrade::Standard, $aug->grade);
        self::assertTrue($aug->active);
        self::assertSame(['soft-zero'], $aug->softs);
    }

    /**
     * Test getCost() on some unmodified standard grade augmentations.
     */
    public function testGetCostSimple(): void
    {
        self::assertSame(3000, $this->augmentation->getCost());
    }

    /**
     * Test getCost() on some augmentations with modifications.
     */
    public function testGetCostWithModifications(): void
    {
        $this->augmentation->modifications[] = new Augmentation('damper');
        self::assertSame(5250, $this->augmentation->getCost());
    }

    /**
     * Test getCost() on some augmentations with modifications and different
     * grade.
     */
    public function testGetCostWithModificationsAndGrade(): void
    {
        $aug = new Augmentation('cyberears-1', 'Beta');
        $aug->modifications[] = new Augmentation('damper', 'Beta');
        self::assertSame(7875, $aug->getCost());
    }

    /**
     * Data provider for cyberware grades.
     * @return array<int, array{0: AugmentationGrade|string, 1: int}>
     */
    public static function cyberwareGradesAndCostProvider(): array
    {
        return [
            [AugmentationGrade::Standard, 4000],
            ['Unknown', 4000],
            [AugmentationGrade::Used, 3000],
            [AugmentationGrade::Alpha, 4800],
            [AugmentationGrade::Beta, 6000],
            [AugmentationGrade::Delta, 10000],
            [AugmentationGrade::Gamma, 4000], // Not supported yet
            [AugmentationGrade::Omega, 4000], // Not supported yet
        ];
    }

    /**
     * Test a modification that has a built-in modification.
     */
    #[DataProvider('cyberwareGradesAndCostProvider')]
    public function testModifiedModification(AugmentationGrade|string $grade, int $cost): void
    {
        $aug = new Augmentation('cybereyes-1', $grade);
        self::assertNotEmpty($aug->modifications);
        self::assertInstanceOf(Augmentation::class, $aug->modifications[0]);
        self::assertSame(0, $aug->modifications[0]->cost);
        self::assertSame($cost, $aug->getCost());
    }

    /**
     * Test trying to find an invalid augmentation by name.
     */
    public function testFindByNameNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Augmentation "Not Found" was not found');
        Augmentation::findByName('Not Found');
    }

    /**
     * Test finding an augmentation by name with a string rating.
     */
    public function testFindByNameWithStringRating(): void
    {
        $augmentation = Augmentation::findByName('Bone Lacing', 'aluminum');
        self::assertSame(18000, $augmentation->cost);
    }

    /**
     * Test finding an augmentation by name with a numeric rating.
     */
    public function testFindByNameWithIntRating(): void
    {
        $aug = Augmentation::findByName('Bone Density Augmentation', 2);
        self::assertSame(10000, $aug->cost);
    }

    /**
     * Test finding an augmentation by name for an item without a rating.
     */
    public function testFindByNameNoRating(): void
    {
        $aug = Augmentation::findByName('damper');
        self::assertSame(2250, $aug->cost);
    }
}
