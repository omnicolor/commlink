<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Controller for Shadowrun 5th Edition martial arts styles.
 * @psalm-suppress UnusedClass
 */
class MartialArtsStylesController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all styles.
     * @var array<string, mixed>
     */
    protected array $styles;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'martial-arts-styles.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/martial-arts-styles';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->styles = require $this->filename;
    }

    /**
     * Get the entire collection of styles.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->styles) as $key) {
            $this->styles[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/martial-arts-styles/%s',
                    \urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->styles),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return information about a single style.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->styles)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $style = $this->styles[$id];
        $style['links']['self'] = $this->links['self'] = \sprintf(
            '/api/shadowrun5e/martial-arts-styles/%s',
            \urlencode($id)
        );

        $this->headers['Etag'] = \sha1((string)\json_encode($style));
        $data = [
            'links' => $this->links,
            'data' => $style,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
