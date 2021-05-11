<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5E;

use Illuminate\Http\Response;

/**
 * Controller for vehicles.
 */
class VehiclesController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all vehicles.
     * @var array<string, mixed>
     */
    protected array $vehicles;

    /**
     * Constructor.
     * @throws \ErrorException if the path to the data file is wrong
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e') . 'vehicles.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/vehicles';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->vehicles = require $this->filename;
    }

    /**
     * Return collection of all vehicles.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->vehicles as $key => $value) {
            $this->vehicles[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/vehicles/%s',
                    \urlencode($key)
                ),
            ];
            $this->vehicles[$key]['ruleset'] ??= 'core';
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->vehicles),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single vehicle.
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->vehicles)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $vehicle = $this->vehicles[$id];
        $this->links['self'] = $vehicle['links']['self']
            = \sprintf('/api/shadowrun5e/vehicles/%s', \urlencode($id));
        $this->headers['Etag'] = \sha1((string)\json_encode($vehicle));

        $data = [
            'links' => $this->links,
            'data' => $vehicle,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
