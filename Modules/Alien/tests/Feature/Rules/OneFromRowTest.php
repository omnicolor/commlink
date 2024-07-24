<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Rules;

use Modules\Alien\Models\Career;
use Modules\Alien\Rules\OneFromRow;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('alien')]
#[Small]
final class OneFromRowTest extends TestCase
{
    public function testFailure(): void
    {
        $career = new Career('colonial-marine');
        $rule = new OneFromRow($career);
        $rule->setData([
            'gear' => [
                'armat-m41ae2-heavy-pulse-rifle',
                'm56a2-smart-gun',
            ],
        ]);

        /** @phpstan-ignore argument.type */
        $rule->validate('unused', 'unused', function (string $message): void {
            self::assertSame(
                'You cannot choose two items from the same row.',
                $message,
            );
        });
    }

    #[DoesNotPerformAssertions]
    public function testSuccessWithEmptyData(): void
    {
        $career = new Career('colonial-marine');
        $rule = new OneFromRow($career);
        $rule->setData([
            'gear' => [],
        ]);

        $rule->validate('unused', 'unused', function (string $message): void {
            self::fail('Validator called $fail Closure');
        });
    }

    #[DoesNotPerformAssertions]
    public function testSuccess(): void
    {
        $career = new Career('colonial-marine');
        $rule = new OneFromRow($career);
        $rule->setData([
            'gear' => [
                'm56a2-smart-gun',
                'm314-motion-tracker',
            ],
        ]);

        $rule->validate('unused', 'unused', function (string $message): void {
            self::fail('Validator called $fail Closure');
        });
    }
}
