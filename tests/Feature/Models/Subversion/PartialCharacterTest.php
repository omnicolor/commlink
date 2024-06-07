<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Subversion;

use App\Models\Subversion\Caste;
use App\Models\Subversion\PartialCharacter;
use Tests\TestCase;

/**
 * @group subversion
 * @small
 */
final class PartialCharacterTest extends TestCase
{
    public function testGetFortuneBrandNew(): void
    {
        $character = new PartialCharacter();
        self::assertSame(
            PartialCharacter::STARTING_FORTUNE,
            $character->fortune,
        );
    }

    public function testGetFortuneWithCaste(): void
    {
        $character = new PartialCharacter(['caste' => 'undercity']);
        $undercity = new Caste('undercity');
        self::assertSame(
            PartialCharacter::STARTING_FORTUNE + $undercity->fortune,
            $character->fortune,
        );

        $elite = new Caste('elite');
        $character->caste = $elite;
        self::assertSame(
            PartialCharacter::STARTING_FORTUNE + $elite->fortune,
            $character->fortune,
        );
    }

    public function testGetFortuneWithCorruptedValue(): void
    {
        $character = new PartialCharacter();
        self::assertSame(
            PartialCharacter::STARTING_FORTUNE,
            $character->fortune,
        );
        $character->corrupted_value = false;
        self::assertSame(
            PartialCharacter::STARTING_FORTUNE,
            $character->fortune,
        );
        $character->corrupted_value = true;
        self::assertSame(
            PartialCharacter::STARTING_FORTUNE + PartialCharacter::CORRUPTED_VALUE_FORTUNE,
            $character->fortune,
        );
    }
}
