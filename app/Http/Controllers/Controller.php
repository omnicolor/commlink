<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use DateTimeImmutable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * Headers to return with the response.
     * @var array<string, mixed>
     */
    protected array $headers;

    /**
     * Links to include in the payload.
     * @var array<string, string>
     */
    protected array $links;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->headers = [
            'Access-Control-Allow-Origin' => '*',
            'Cache-Control' => 'public',
            'Content-Language' => 'en-US',
            'Expires' => (new DateTimeImmutable('+1 month'))->format('r'),
        ];
        $this->links = ['root' => '/api'];
    }

    /**
     * Return a JSON-API error.
     * @param array<string, string|int> $error
     */
    public function error(array $error): Response
    {
        $data = [
            'links' => $this->links,
            'errors' => [$error],
        ];
        return response($data, (int)$error['status'])
            ->withHeaders($this->headers);
    }
}
