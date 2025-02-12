<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Middleware;

use App\Http\Middleware\DefaultToAcceptJson;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Override;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Small]
final class DefaultToAcceptJsonTest extends TestCase
{
    private DefaultToAcceptJson $middleware;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new DefaultToAcceptJson();
    }

    public function testSetAcceptIfNotSent(): void
    {
        $request = Request::create('http://example.com/api/testing', 'GET');
        $request->headers->set('Accept', null);

        $this->middleware->handle($request, function (Request $request): Response {
            self::assertSame('application/json', $request->headers->get('Accept'));
            return new Response();
        });
    }

    public function testDoNotOverride(): void
    {
        $request = Request::create('http://example.com/api/testing', 'GET');
        $request->headers->set('Accept', 'text/plain');

        $this->middleware->handle($request, function () use ($request): Response {
            self::assertSame('text/plain', $request->headers->get('Accept'));
            return new Response();
        });
    }
}
