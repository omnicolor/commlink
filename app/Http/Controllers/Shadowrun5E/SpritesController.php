<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5E;

use Illuminate\Http\Response;

/**
 * Controller for technomancer sprites.
 */
class SpritesController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all sprites.
     * @var array<string, mixed>
     */
    protected array $sprites;

    /**
     * Constructor.
     * @throws \ErrorException if the path to the data file is wrong
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_url') . 'sprites.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/sprites';
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        $this->sprites = require $this->filename;
    }

    /**
     * Get the entire collection of Shadowrun 5E sprites.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->sprites as $key => $unused) {
            $this->sprites[$key]['links'] = [
                'self' => sprintf('/api/shadowrun5e/sprites/%s', $key),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->sprites),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single Shadowrun technomancer sprite.
     * @param string $identifier
     * @return \Illuminate\Http\Response
     */
    public function show(string $identifier): Response
    {
        $identifier = strtolower($identifier);
        if (!key_exists($identifier, $this->sprites)) {
            // We couldn't find it!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => sprintf('%s not found', $identifier),
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $sprite = $this->sprites[$identifier];
        $sprite['links']['self'] = $this->links['self'] =
            sprintf('/api/shadowrun5e/sprites/%s', $identifier);

        $this->headers['Etag'] = sha1((string)json_encode($sprite));

        $data = [
            'links' => $this->links,
            'data' => $sprite,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
