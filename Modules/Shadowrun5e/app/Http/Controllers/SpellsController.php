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
 * Spells API route.
 */
class SpellsController extends Controller
{
    /**
     * Filename for the data file.
     */
    protected string $filename;

    /**
     * Spells collection.
     * @var array<string, mixed>
     */
    protected array $spells;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path') . 'spells.php';
        $this->links['collection'] = route('shadowrun5e.spells.index');

        $this->spells = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = Carbon::createFromTimestamp($stat['mtime'])->format('r');
    }

    public function index(): Response
    {
        foreach (array_keys($this->spells) as $key) {
            $this->spells[$key]['links']['self'] =
                route('shadowrun5e.spells.show', $key);
            $this->spells[$key]['ruleset'] ??= 'core';
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->spells),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->spells),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );

        $spell = $this->spells[$id];
        $spell['links']['self'] = $this->links['self'] =
            route('shadowrun5e.spells.show', $id);

        $this->headers['Etag'] = sha1((string)json_encode($spell));

        $data = [
            'links' => $this->links,
            'data' => $spell,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
