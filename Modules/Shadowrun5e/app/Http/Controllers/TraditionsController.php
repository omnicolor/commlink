<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Traditions route.
 * @psalm-suppress UnusedClass
 */
class TraditionsController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all magical traditions.
     * @var array<string, mixed>
     */
    protected array $traditions;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'traditions.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/traditions';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->traditions = require $this->filename;
    }

    /**
     * Get the entire collection.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->traditions) as $key) {
            $this->traditions[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/traditions/%s',
                    \urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->traditions),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single tradition.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $identifier): Response
    {
        $identifier = \strtolower($identifier);
        if (!\array_key_exists($identifier, $this->traditions)) {
            // We couldn't find it!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => \sprintf('%s not found', $identifier),
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $tradition = $this->traditions[$identifier];
        $tradition['links']['self'] = $this->links['self'] =
            \sprintf('/api/shadowrun5e/traditions/%s', $identifier);

        $this->headers['Etag'] = \sha1((string)\json_encode($tradition));

        $data = [
            'links' => $this->links,
            'data' => $tradition,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
