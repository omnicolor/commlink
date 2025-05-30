<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function abort_if;
use function array_key_exists;
use function array_keys;
use function array_values;
use function assert;
use function config;
use function date;
use function json_encode;
use function response;
use function route;
use function sha1;
use function sha1_file;
use function stat;
use function strtolower;

/**
 * Controller for vehicles.
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

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path') . 'vehicles.php';
        $this->links['collection'] = route('shadowrun5e.vehicles.index');

        $this->vehicles = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
    }

    /**
     * Return collection of all vehicles.
     */
    public function index(): Response
    {
        foreach (array_keys($this->vehicles) as $key) {
            $this->vehicles[$key]['links'] = [
                'self' => route('shadowrun5e.vehicles.show', $key),
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
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->vehicles),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );

        $vehicle = $this->vehicles[$id];
        $this->links['self'] = $vehicle['links']['self']
            = route('shadowrun5e.vehicles.show', $id);
        $this->headers['Etag'] = sha1((string)json_encode($vehicle));

        $data = [
            'links' => $this->links,
            'data' => $vehicle,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
