<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Controller for Shadowrun augmentations.
 * @psalm-suppress UnusedClass
 */
class CyberwareController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all augmentations.
     * @var array<string, mixed>
     */
    protected array $augmentations;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path') . 'cyberware.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/cyberware';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->augmentations = require $this->filename;
    }

    /**
     * Get the entire collection of Shadowrun 5E augmentations.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->augmentations) as $key) {
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
     * @psalm-suppress PossiblyUnusedMethod
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
