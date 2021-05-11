<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5E;

use Illuminate\Http\Response;

/**
 * Controller for Shadowrun 5E gear.
 */
class GearController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all gear.
     * @var array<string, mixed>
     */
    protected array $gear;

    /**
     * Constructor.
     * @throws \ErrorException if the path to the data file is wrong
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e') . 'gear.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/gear';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->gear = require $this->filename;
    }

    /**
     * Get the entire collection of Shadowrun 5e gear.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->gear as $key => $unused) {
            $this->gear[$key]['links'] = [
                'self' => \sprintf('/api/shadowrun5e/gear/%s', \urlencode($key)),
            ];
            $this->gear[$key]['ruleset'] ??= 'core';
        }

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->gear),
        ];

        $this->headers['Etag'] = \sha1_file($this->filename);
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single item.
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->gear)) {
            // We couldn't find it!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $item = $this->gear[$id];
        $item['ruleset'] ??= 'core';
        $item['links']['self'] = $this->links['self'] =
            \sprintf('/api/shadowrun5e/gear/%s', \urlencode($id));

        $data = [
            'links' => $this->links,
            'data' => $item,
        ];

        $this->headers['Etag'] = \sha1((string)\json_encode($item));
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
