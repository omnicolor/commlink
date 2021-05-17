<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5E;

use Illuminate\Http\Response;

/**
 * Controller for Shadowrun armor.
 */
class ArmorController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all armor.
     * @var array<string, mixed>
     */
    protected array $armor;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e') . 'armor.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/armor';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->armor = require $this->filename;
    }

    /**
     * Get the entire collection of Shadowrun 5E armor.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->armor as $key => $value) {
            $this->armor[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/armor/%s',
                    \urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->armor),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single 5E armor.
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->armor)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $armor = $this->armor[$id];
        $armor['ruleset'] ??= 'core';
        $armor['links']['self'] = $this->links['self']
            = \sprintf('/api/shadowrun5e/armor/%s', \urlencode($id));

        $this->headers['Etag'] = \sha1((string)\json_encode($armor));

        $data = [
            'links' => $this->links,
            'data' => $armor,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
