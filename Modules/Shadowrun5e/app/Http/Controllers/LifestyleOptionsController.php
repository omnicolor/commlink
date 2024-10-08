<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Controller for Shadowrun 5th Edition lifestyle options.
 * @psalm-suppress UnusedClass
 */
class LifestyleOptionsController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all options.
     * @var array<string, mixed>
     */
    protected array $options;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'lifestyle-options.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/lifestyle-options';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->options = require $this->filename;
    }

    /**
     * Return collection of all lifestyle options.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->options) as $key) {
            $this->options[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/lifestyle-options/%s',
                    \urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->options),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single lifestyle option.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->options)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $option = $this->options[$id];
        $this->links['self'] = $option['links']['self']
            = \sprintf('/api/shadowrun5e/lifestyle-options/%s', \urlencode($id));
        $this->headers['Etag'] = \sha1((string)\json_encode($option));

        $data = [
            'links' => $this->links,
            'data' => $option,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
