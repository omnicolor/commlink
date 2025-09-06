<?php

declare(strict_types=1);

namespace Tests\Feature\ValueObjects;

use App\ValueObjects\Email;
use InvalidArgumentException;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Small]
final class EmailTest extends TestCase
{
    /**
     * @return Iterator<int, array<int, string>>
     */
    public static function validEmailProvider(): Iterator
    {
        yield ['bob@example.com'];
        yield ['bob+something@example.org'];
        yield ['bob_king@example.co.uk'];
    }

    #[DataProvider('validEmailProvider')]
    public function testValidEmail(string $address): void
    {
        $email = new Email($address);
        self::assertSame($address, (string)$email);
    }

    /**
     * @return Iterator<int, array<int, string>>
     */
    public static function invalidEmailProvider(): Iterator
    {
        yield [''];
        yield ['@foo'];
        yield ['@example.com'];
        yield ['bob'];
        yield ['bob@.com'];
        yield ['bob@gmail'];
    }

    #[DataProvider('invalidEmailProvider')]
    public function testInvalidEmail(string $address): void
    {
        self::expectException(InvalidArgumentException::class);
        new Email($address);
    }
}
