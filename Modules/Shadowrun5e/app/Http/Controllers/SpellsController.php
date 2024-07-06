<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Spells API route.
 * @psalm-suppress UnusedClass
 */
class SpellsController extends Controller
{
    /**
     * Filename for the data file.
     */
    protected string $filename;

    /**
     * Spells collection.
     * @var array<string, mixed>
     */
    protected array $spells;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path') . 'spells.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/spells';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->spells = require $this->filename;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->spells) as $key) {
            $this->spells[$key]['links']['self'] = \sprintf(
                '/api/shadowrun5e/spells/%s',
                \urlencode($key)
            );
            $this->spells[$key]['ruleset'] ??= 'core';
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->spells),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->spells)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $spell = $this->spells[$id];
        $spell['links']['self'] = $this->links['self'] = \sprintf(
            '/api/shadowrun5e/spells/%s',
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
