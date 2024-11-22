<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Controller for Shadowrun 5th Edition lifestyle zones.
 * @psalm-suppress UnusedClass
 */
class LifestyleZonesController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all zones.
     * @var array<string, mixed>
     */
    protected array $zones;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'lifestyle-zones.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/lifestyle-zones';

        /** @psalm-suppress UnresolvableInclude */
        $this->zones = require $this->filename;

        $stat = \stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
    }

    /**
     * Return collection of all lifestyle zones.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->zones) as $key) {
            $this->zones[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/lifestyle-zones/%s',
                    \urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->zones),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single lifestyle zones.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->zones)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $zone = $this->zones[$id];
        $this->links['self'] = $zone['links']['self']
            = \sprintf('/api/shadowrun5e/lifestyle-zones/%s', \urlencode($id));
        $this->headers['Etag'] = \sha1((string)\json_encode($zone));

        $data = [
            'links' => $this->links,
            'data' => $zone,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
