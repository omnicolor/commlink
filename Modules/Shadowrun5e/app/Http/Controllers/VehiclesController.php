<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function array_key_exists;
use function array_keys;
use function array_values;
use function config;
use function date;
use function json_encode;
use function response;
use function sha1;
use function sha1_file;
use function sprintf;
use function stat;
use function strtolower;
use function urlencode;

/**
 * Controller for vehicles.
 * @psalm-suppress UnusedClass
 */
class VehiclesController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all vehicles.
     * @var array<string, mixed>
     */
    protected array $vehicles;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path') . 'vehicles.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/vehicles';

        /** @psalm-suppress UnresolvableInclude */
        $this->vehicles = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
    }

    /**
     * Return collection of all vehicles.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->vehicles) as $key) {
            $this->vehicles[$key]['links'] = [
                'self' => sprintf(
                    '/api/shadowrun5e/vehicles/%s',
                    urlencode($key)
                ),
            ];
            $this->vehicles[$key]['ruleset'] ??= 'core';
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->vehicles),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single vehicle.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        if (!array_key_exists($id, $this->vehicles)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $vehicle = $this->vehicles[$id];
        $this->links['self'] = $vehicle['links']['self']
            = sprintf('/api/shadowrun5e/vehicles/%s', urlencode($id));
        $this->headers['Etag'] = sha1((string)json_encode($vehicle));

        $data = [
            'links' => $this->links,
            'data' => $vehicle,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
