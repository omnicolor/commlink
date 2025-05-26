<?php

declare(strict_types=1);

namespace Tests\Feature\ValueObjects;

use App\ValueObjects\Email;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Small]
final class EmailTest extends TestCase
{
    /**
     * @return array<int, array<int, string>>
     */
    public static function validEmailProvider(): array
    {
        return [
            ['bob@example.com'],
            ['bob+something@example.org'],
            ['bob_king@example.co.uk'],
        ];
    }

    #[DataProvider('validEmailProvider')]
    public function testValidEmail(string $address): void
    {
        $email = new Email($address);
        self::assertSame($address, (string)$email);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public static function invalidEmailProvider(): array
    {
        return [
            [''],
            ['@foo'],
            ['@example.com'],
            ['bob'],
            ['bob@.com'],
            ['bob@gmail'],
        ];
    }

    #[DataProvider('invalidEmailProvider')]
    public function testInvalidEmail(string $address): void
    {
        self::expectException(InvalidArgumentException::class);
        new Email($address);
    }
}
