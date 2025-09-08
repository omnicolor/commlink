<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\Models;

use Modules\Stillfleet\Models\Power;
use Modules\Stillfleet\Models\Species;
use Override;
use Tests\TestCase;

final class SpeciesTest extends TestCase
{
    private Species $species;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->species = Species::findOrFail('fleeter');
    }

    public function testToString(): void
    {
        self::assertSame('Fleeter', (string)$this->species);
    }

    public function testAddPowers(): void
    {
        self::assertCount(0, $this->species->added_powers);
        $this->species->addPowers(Power::findOrFail('arkheion-access'));
        self::assertCount(1, $this->species->added_powers);
    }

    public function testSpeciesPowers(): void
    {
        self::assertCount(3, $this->species->species_powers);
    }

    public function testOptionalPowers(): void
    {
        self::assertCount(2, $this->species->optional_powers);
    }
}
