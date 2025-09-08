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
 * Controller for technomancer sprites.
 */
class SpritesController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all sprites.
     * @var array<string, mixed>
     */
    protected array $sprites;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path') . 'sprites.php';
        $this->links['collection'] = route('shadowrun5e.sprites.index');

        $this->sprites = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = Carbon::createFromTimestamp($stat['mtime'])->format('r');
    }

    public function index(): Response
    {
        foreach (array_keys($this->sprites) as $key) {
            $this->sprites[$key]['links'] = [
                'self' => route('shadowrun5e.sprites.show', $key),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->sprites),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    public function show(string $identifier): Response
    {
        $identifier = strtolower($identifier);
        abort_if(
            !array_key_exists($identifier, $this->sprites),
            Response::HTTP_NOT_FOUND,
            $identifier . ' not found',
        );

        $sprite = $this->sprites[$identifier];
        $sprite['links']['self'] = $this->links['self'] =
            route('shadowrun5e.sprites.show', $identifier);

        $this->headers['Etag'] = sha1((string)json_encode($sprite));

        $data = [
            'links' => $this->links,
            'data' => $sprite,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
