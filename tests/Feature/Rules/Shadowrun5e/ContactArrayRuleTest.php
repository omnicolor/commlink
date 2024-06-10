<?php

declare(strict_types=1);

namespace Tests\Feature\Rules\Shadowrun5e;

use App\Rules\Shadowrun5e\ContactArrayRule;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class ContactArrayRuleTest extends TestCase
{
    public function testFailure(): void
    {
        $data = [
            'contact-names' => [
                'bob',
            ],
        ];

        $rule = new ContactArrayRule();
        $rule->setData($data);
        // @phpstan-ignore-next-line
        $rule->validate('unused', 'unused', function (string $message): void {
            self::assertSame(
                'One or more contacts are missing some data',
                $message,
            );
        });
    }

    #[DoesNotPerformAssertions]
    public function testSuccessEmpty(): void
    {
        $rule = new ContactArrayRule();
        $rule->setData([]);
        $rule->validate('unused', 'unused', function (string $message): void {
            self::fail('Validator called $fail Closure');
        });
    }

    #[DoesNotPerformAssertions]
    public function testSuccess(): void
    {
        $data = [
            'contact-names' => [
                'Bob King',
            ],
            'contact-archetypes' => [
                'Fixer',
            ],
            'contact-connections' => [
                6,
            ],
            'contact-loyalties' => [
                6,
            ],
            'contact-notes' => [
                'One bad-assed mofo',
            ],
        ];

        $rule = new ContactArrayRule();
        $rule->setData($data);
        $rule->validate('unused', 'unused', function (string $message): void {
            self::fail('Validator called $fail Closure');
        });
    }
}
