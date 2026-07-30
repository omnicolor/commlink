<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Models;

use Modules\Shadowrun6e\Models\ComplexForm;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class ComplexFormTest extends TestCase
{
    public function testToString(): void
    {
        $form = ComplexForm::findOrFail('cleaner');
        self::assertSame('Cleaner', (string)$form);
    }
}
