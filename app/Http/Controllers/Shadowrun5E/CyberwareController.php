<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5E;

use Illuminate\Http\Response;

/**
 * Controller for Shadowrun augmentations.
 */
class CyberwareController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all augmentations.
     * @var array<string, mixed>
     */
    protected array $augmentations;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e') . 'cyberware.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/cyberware';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->augmentations = require $this->filename;
    }

    /**
     * Get the entire collection of Shadowrun 5E augmentations.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->augmentations as $key => $unused) {
            $this->augmentations[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/cyberware/%s',
                    \urlencode($key)
                ),
            ];
            $this->augmentations['ruleset'] ??= 'core';
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->augmentations),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single Shadowrun 5E augmentation.
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->augmentations)) {
            // We couldn't find it!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $cyberware = $this->augmentations[$id];
        $cyberware['ruleset'] ??= 'core';
        $cyberware['links']['self'] = $this->links['self'] =
            \sprintf('/api/shadowrun5e/cyberware/%s', $id);

        $this->headers['Etag'] = \sha1((string)\json_encode($cyberware));

        // Return the single item.
        $data = [
            'links' => $this->links,
            'data' => $cyberware,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
