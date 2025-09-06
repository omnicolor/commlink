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
 * Traditions route.
 */
class TraditionsController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all magical traditions.
     * @var array<string, mixed>
     */
    protected array $traditions;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path') . 'traditions.php';
        $this->links['collection'] = route('shadowrun5e.traditions.index');

        $this->traditions = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = Carbon::createFromTimestamp($stat['mtime'])->format('r');
    }

    /**
     * Get the entire collection.
     */
    public function index(): Response
    {
        foreach (array_keys($this->traditions) as $key) {
            $this->traditions[$key]['links'] = [
                'self' => route('shadowrun5e.traditions.show', $key),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->traditions),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single tradition.
     */
    public function show(string $identifier): Response
    {
        $identifier = strtolower($identifier);
        abort_if(
            !array_key_exists($identifier, $this->traditions),
            Response::HTTP_NOT_FOUND,
            $identifier . ' not found',
        );

        $tradition = $this->traditions[$identifier];
        $tradition['links']['self'] = $this->links['self'] =
            route('shadowrun5e.traditions.show', $identifier);

        $this->headers['Etag'] = sha1((string)json_encode($tradition));

        $data = [
            'links' => $this->links,
            'data' => $tradition,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
