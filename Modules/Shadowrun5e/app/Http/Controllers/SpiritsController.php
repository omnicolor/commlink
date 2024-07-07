<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Spirits API route.
 * @psalm-suppress UnusedClass
 */
class SpiritsController extends Controller
{
    /**
     * Filename for the data file.
     */
    protected string $filename;

    /**
     * Spirits collection.
     * @var array<string, mixed>
     */
    protected array $spirits;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path') . 'spirits.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/spirits';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->spirits = require $this->filename;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->spirits) as $key) {
            $this->spirits[$key]['links']['self'] = \sprintf(
                '/api/shadowrun5e/spirits/%s',
                \urlencode($key)
            );
            $this->spirits[$key]['ruleset'] ??= 'core';
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->spirits),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->spirits)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $spell = $this->spirits[$id];
        $spell['links']['self'] = $this->links['self'] = \sprintf(
            '/api/shadowrun5e/spirits/%s',
            \urlencode($id)
        );

        $this->headers['Etag'] = \sha1((string)\json_encode($spell));

        $data = [
            'links' => $this->links,
            'data' => $spell,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
