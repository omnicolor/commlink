<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5E;

use Illuminate\Http\Response;

/**
 * Spells API route.
 */
class SpellsController extends \App\Http\Controllers\Controller
{
    /**
     * Filename for the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Spells collection.
     * @var array<string, mixed>
     */
    protected array $spells;

    /**
     * Constructor.
     * @throws \ErrorException if the path to the data file is wrong
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e') . 'spells.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/spells';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->spells = require $this->filename;
    }

    /**
     * Return the entire collection of spells.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->spells as $key => $value) {
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
     * Return an individual spell.
     * @return \Illuminate\Http\Response
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
