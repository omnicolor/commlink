<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Response;

use function abort_if;
use function array_key_exists;
use function array_keys;
use function array_values;
use function assert;
use function config;
use function json_encode;
use function response;
use function route;
use function sha1;
use function sha1_file;
use function stat;
use function strtolower;

/**
 * Vehicle modifications for Shadowrun 5E controller.
 */
class VehicleModificationsController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all vehicle modifications.
     * @var array<string, mixed>
     */
    protected array $mods;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'vehicle-modifications.php';
        $this->links['collection'] = route('shadowrun5e.vehicle-modifications.index');

        $this->mods = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = Carbon::createFromTimestamp($stat['mtime'])->format('r');
    }

    /**
     * Return a collection of all modifications.
     */
    public function index(): Response
    {
        foreach (array_keys($this->mods) as $key) {
            $this->mods[$key]['links'] = [
                'self' => route('shadowrun5e.vehicle-modifications.show', $key),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $data = [
            'links' => $this->links,
            'data' => array_values($this->mods),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single vehicle modification.
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->mods),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );

        $mod = $this->mods[$id];
        $this->links['self'] = $mod['links']['self'] =
            route('shadowrun5e.vehicle-modifications.show', $id);
        $this->headers['Etag'] = sha1((string)json_encode($mod));
        $data = [
            'links' => $this->links,
            'data' => $mod,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
