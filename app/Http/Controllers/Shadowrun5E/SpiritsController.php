<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5E;

use Illuminate\Http\Response;

/**
 * Spirits API route.
 */
class SpiritsController extends \App\Http\Controllers\Controller
{
    /**
     * Filename for the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Spirits collection.
     * @var array<string, mixed>
     */
    protected array $spirits;

    /**
     * Constructor.
     * @throws \ErrorException if the path to the data file is wrong
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e') . 'spirits.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/spirits';
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        $this->spirits = require $this->filename;
    }

    /**
     * Return the entire collection of spirits.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->spirits as $key => $value) {
            $this->spirits[$key]['links']['self'] = sprintf(
                '/api/shadowrun5e/spirits/%s',
                urlencode($key)
            );
            $this->spirits[$key]['ruleset'] ??= 'core';
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->spirits),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return an individual spell.
     * @return \Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        if (!array_key_exists($id, $this->spirits)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $spell = $this->spirits[$id];
        $spell['links']['self'] = $this->links['self'] = sprintf(
            '/api/shadowrun5e/spirits/%s',
            urlencode($id)
        );

        $this->headers['Etag'] = sha1((string)json_encode($spell));

        $data = [
            'links' => $this->links,
            'data' => $spell,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
