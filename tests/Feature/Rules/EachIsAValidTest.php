<?php

declare(strict_types=1);

namespace Tests\Feature\Rules;

use App\Models\Subversion\RelationAspect;
use App\Rules\EachIsAValid;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('subversion')]
#[Small]
final class EachIsAValidTest extends TestCase
{
    public function testFailureWrongType(): void
    {
        $rule = new EachIsAValid(RelationAspect::class);
        // @phpstan-ignore-next-line
        $rule->validate('unused', 'unknown', function (string $message): void {
            self::assertSame('Value contains one or more invalid items.', $message);
        });
    }

    #[DoesNotPerformAssertions]
    public function testSuccessEmpty(): void
    {
        $rule = new EachIsAValid(RelationAspect::class);
        $rule->validate('unused', '', function (string $message): void {
            self::fail('Validator called $fail Closure');
        });
    }

    #[DoesNotPerformAssertions]
    public function testSuccess(): void
    {
        $rule = new EachIsAValid(RelationAspect::class);
        $rule->validate('unused', 'adversarial,dues', function (string $message): void {
            self::fail('Validator called $fail Closure');
        });
    }
}
