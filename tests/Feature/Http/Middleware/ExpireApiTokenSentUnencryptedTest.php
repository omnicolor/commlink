<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Middleware;

use App\Http\Middleware\ExpireApiTokenSentUnencrypted;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Override;
use PHPUnit\Framework\Attributes\Small;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

#[Small]
final class ExpireApiTokenSentUnencryptedTest extends TestCase
{
    private ExpireApiTokenSentUnencrypted $middleware;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new ExpireApiTokenSentUnencrypted();
    }

    public function testUnencrypted(): void
    {
        $token = self::createMock(PersonalAccessToken::class);
        $token->expects(self::once())->method('delete');

        $user = self::createStub(User::class);
        $user->method('currentAccessToken')->willReturn($token);

        $request = Request::create('http://example.com/testing', 'GET');
        $request->setUserResolver(function () use ($user): User {
            return $user;
        });

        self::expectException(HttpException::class);
        self::expectExceptionMessage(
            'Your API key has been revoked. Do not use API keys on an '
                . 'unsecured connection.',
        );
        $this->middleware->handle($request, function (): never {
            self::fail('Closure called');
        });
    }

    public function testUnencryptedNoToken(): void
    {
        $user = self::createStub(User::class);
        $user->method('currentAccessToken')->willReturn(null);

        $request = Request::create('http://example.com/testing', 'GET');
        $request->setUserResolver(function () use ($user): User {
            return $user;
        });

        $was_called = false;
        $this->middleware->handle($request, function () use (&$was_called): void {
            $was_called = true;
        });
        self::assertTrue($was_called, 'Middleware closure was not called');
    }

    public function testEncrypted(): void
    {
        $token = self::createMock(PersonalAccessToken::class);
        $token->expects(self::never())->method('delete');

        $user = self::createStub(User::class);
        $user->method('currentAccessToken')->willReturn($token);

        $request = Request::create('https://example.com/testing', 'GET');
        $request->setUserResolver(function () use ($user): User {
            return $user;
        });

        $was_called = false;
        $this->middleware->handle($request, function () use (&$was_called): void {
            $was_called = true;
        });
        self::assertTrue($was_called, 'Middleware closure was not called');
    }
}
