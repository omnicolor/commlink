<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Identity;
use App\Models\Shadowrun5e\License;
use App\Models\Shadowrun5e\Lifestyle;
use App\Models\Shadowrun5e\LifestyleOption;
use Tests\TestCase;

/**
 * Tests for Identity class.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class IdentityTest extends TestCase
{
    /**
     * Test creating an identity with the minimum amount of info.
     * @test
     */
    public function testFromArrayMinimum(): void
    {
        $example = [
            'id' => 0,
            'licenses' => [],
            'name' => 'Burner',
            'sin' => 1,
        ];
        $identity = Identity::fromArray($example);
        self::assertSame(0, $identity->identifier);
        self::assertSame('Burner', $identity->name);
        self::assertEmpty($identity->licenses);
        self::assertEmpty($identity->lifestyles);
        self::assertSame(1, $identity->sin);
        self::assertNull($identity->sinner);
    }

    /**
     * Test converting an identity to a string.
     * @test
     */
    public function testToString(): void
    {
        $example = [
            'id' => 42,
            'licenses' => [],
            'name' => 'Elvis',
            'sin' => 1,
        ];
        $identity = Identity::fromArray($example);
        self::assertSame('Elvis', (string) $identity);
    }

    /**
     * Test creating a SINner identity.
     * @test
     */
    public function testFromArraySinner(): void
    {
        $example = [
            'id' => 0,
            'licenses' => [],
            'name' => 'Burner',
            'sin' => 'national',
            'sinner' => true,
        ];
        $identity = Identity::fromArray($example);
        self::assertSame(0, $identity->identifier);
        self::assertSame('Burner', $identity->name);
        self::assertEmpty($identity->licenses);
        self::assertEmpty($identity->lifestyles);
        self::assertNull($identity->sin);
        self::assertSame('national', $identity->sinner);
    }

    /**
     * Test creating an identity that has lifestyles.
     * @test
     */
    public function testLifestyles(): void
    {
        $example = [
            'id' => 0,
            'lifestyles' => [
                [
                    'name' => 'Street',
                    'quantity' => 1,
                    'options' => ['increase-neighborhood', 'swimming-pool'],
                ],
            ],
            'name' => 'Burner',
            'sin' => 'national',
            'sinner' => true,
        ];
        $identity = Identity::fromArray($example);
        self::assertNotEmpty($identity->lifestyles);
        self::assertInstanceOf(Lifestyle::class, $identity->lifestyles[0]);
        self::assertNotEmpty($identity->lifestyles[0]->options);
        self::assertInstanceOf(
            LifestyleOption::class,
            $identity->lifestyles[0]->options[0]
        );
    }

    /**
     * Test creating an identity that has a license.
     * @test
     */
    public function testLicenses(): void
    {
        $example = [
            'id' => 0,
            'licenses' => [
                ['rating' => 4, 'license' => 'Concealed Carry'],
            ],
            'name' => 'Burner',
            'sin' => 'national',
            'sinner' => true,
        ];
        $identity = Identity::fromArray($example);
        self::assertNotEmpty($identity->licenses);
        $license = $identity->licenses[0];
        self::assertSame(4, $license->rating);
        self::assertSame('Concealed Carry', $license->name);
    }

    /**
     * Test creating an identity with a lifestyle trying to load an option
     * we're not ready for.
     * @test
     */
    public function testNotFound(): void
    {
        $example = [
            'id' => 0,
            'name' => 'Burner',
            'notes' => 'Test',
            'sin' => 3,
            'lifestyles' => [
                [
                    'name' => 'Street',
                    'quantity' => 1,
                    'options' => ['Not Found'],
                ],
            ],
        ];
        $expected = 'Test' . \PHP_EOL
            . 'Option "Not Found" was not found for lifestyle "Street"';
        $identity = Identity::fromArray($example);
        self::assertEmpty($identity->lifestyles[0]->options);
        self::assertSame($expected, $identity->notes);
    }

    /**
     * Test getting the cost of an identity with nothing attached.
     * @test
     */
    public function testGetCostNothing(): void
    {
        $identity = new Identity();
        self::assertSame(0, $identity->getCost());
    }

    /**
     * Test getting the cost of an identity with some fake SINs.
     * @test
     */
    public function testGetCostWithFakeSin(): void
    {
        $identity = new Identity();
        $identity->sin = 1;
        self::assertSame(2500, $identity->getCost());
        $identity->sin = 6;
        self::assertSame(15000, $identity->getCost());
    }

    /**
     * Test getting the cost of an identity with some fake licenses.
     * @test
     */
    public function testGetCostWithFakeLicense(): void
    {
        $identity = new Identity();
        $identity->licenses[] = new License(1, 'Drivers');
        self::assertSame(200, $identity->getCost());
        $identity->licenses[] = new License(6, 'Pilots');
        self::assertSame(1400, $identity->getCost());
    }

    /**
     * Test getting the cost of an identity with some lifestyles.
     * @test
     */
    public function testGetCostWithLifestyles(): void
    {
        $identity = new Identity();
        $lifestyle = new Lifestyle('middle');
        $lifestyle->options[] = new LifestyleOption('swimming-pool');
        $identity->lifestyles[] = $lifestyle;
        self::assertSame(5000, $identity->getCost());
    }
}
