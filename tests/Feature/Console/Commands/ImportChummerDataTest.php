<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\ImportChummerData;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Medium]
#[Group('shadowrun5e')]
final class ImportChummerDataTest extends TestCase
{
    private ImportChummerData $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new ImportChummerData();
    }

    /**
     * @return array<string, array<int, array<int<1, max>, string>|string>>
     */
    public static function fixedValuesProvider(): array
    {
        return [
            'Numeric example' => [
                'FixedValues(500,1000,2500,5000)',
                [1 => '500', '1000', '2500', '5000'],
            ],
            'String example' => [
                'FixedValues(A,B,C)',
                [1 => 'A', 'B', 'C'],
            ],
            'Formula' => [
                'FixedValues(Rating*1000,Rating*1000,Rating*1000,Rating*2000,Rating*2000,Rating*2000)',
                [
                    1 => 'Rating*1000',
                    'Rating*1000',
                    'Rating*1000',
                    'Rating*2000',
                    'Rating*2000',
                    'Rating*2000',
                ],
            ],
            'Availability' => [
                'FixedValues(6F,6F,6F,9F,9F,9F)',
                [
                    1 => '6F',
                    '6F',
                    '6F',
                    '9F',
                    '9F',
                    '9F',
                ],
            ],
        ];
    }

    /**
     * @param array<int<1, max>, string> $expected
     */
    #[DataProvider('fixedValuesProvider')]
    public function testFixedValuesToArray(string $example, array $expected): void
    {
        $values = $this->command->fixedValuesToArray($example);
        self::assertSame($expected, $values);
    }

    public function testCalculateValueFromFormula(): void
    {
        self::assertSame(
            6000,
            $this->command->calculateValueFromFormula('Rating * 3000', 2),
        );
    }
}
