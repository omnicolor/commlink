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
 * Controller for critter weaknesses.
 */
class CritterWeaknessesController extends Controller
{
    /**
     * Filename for all of the data.
     */
    protected string $filename;

    /**
     * Collection of critter weaknesses.
     * @var array<string, mixed>
     */
    protected array $weaknesses;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'critter-weaknesses.php';

        $this->weaknesses = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = Carbon::createFromTimestamp($stat['mtime'])->format('r');
    }

    /**
     * Get the entire collection of fifth edition critter weaknesses.
     */
    public function index(): Response
    {
        foreach (array_keys($this->weaknesses) as $key) {
            $this->weaknesses[$key]['links'] = [
                'self' => route('shadowrun5e.critter-weaknesses.show', $key),
            ];
            $this->weaknesses[$key]['id'] = $key;
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $this->links['self'] = route('shadowrun5e.critter-weaknesses.index');
        $data = [
            'links' => $this->links,
            'data' => array_values($this->weaknesses),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single fifth edition critter weaknesses.
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->weaknesses),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );
        $weakness = $this->weaknesses[$id];
        $weakness['id'] = $id;
        $weakness['links'] = [
            'self' => route('shadowrun5e.critter-weaknesses.show', $id),
        ];

        $this->headers['Etag'] = sha1((string)json_encode($weakness));
        $this->links['collection'] = route('shadowrun5e.critter-weaknesses.index');
        $this->links['self'] = route('shadowrun5e.critter-weaknesses.show', $id);
        $data = [
            'links' => $this->links,
            'data' => $weakness,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
