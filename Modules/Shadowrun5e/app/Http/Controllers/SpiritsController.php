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
 * Spirits API route.
 */
class SpiritsController extends Controller
{
    /**
     * Filename for the data file.
     */
    protected string $filename;

    /**
     * Spirits collection.
     * @var array<string, mixed>
     */
    protected array $spirits;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path') . 'spirits.php';
        $this->links['collection'] = route('shadowrun5e.spirits.index');

        $this->spirits = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = Carbon::createFromTimestamp($stat['mtime'])->format('r');
    }

    public function index(): Response
    {
        foreach (array_keys($this->spirits) as $key) {
            $this->spirits[$key]['links']['self'] =
                route('shadowrun5e.spirits.show', $key);
            $this->spirits[$key]['ruleset'] ??= 'core';
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->spirits),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->spirits),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );

        $spell = $this->spirits[$id];
        $spell['links']['self'] = $this->links['self'] =
            route('shadowrun5e.spirits.show', $id);

        $this->headers['Etag'] = sha1((string)json_encode($spell));

        $data = [
            'links' => $this->links,
            'data' => $spell,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
