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
 * Controller for Shadowrun 5th Edition lifestyle options.
 */
class LifestyleOptionsController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all options.
     * @var array<string, mixed>
     */
    protected array $options;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'lifestyle-options.php';
        $this->links['collection'] = route('shadowrun5e.lifestyle-options.index');

        $this->options = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = Carbon::createFromTimestamp($stat['mtime'])->format('r');
    }

    /**
     * Return collection of all lifestyle options.
     */
    public function index(): Response
    {
        foreach (array_keys($this->options) as $key) {
            $this->options[$key]['links'] = [
                'self' => route('shadowrun5e.lifestyle-options.show', $key),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->options),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single lifestyle option.
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->options),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );

        $option = $this->options[$id];
        $this->links['self'] = $option['links']['self']
            = route('shadowrun5e.lifestyle-options.show', $id);
        $this->headers['Etag'] = sha1((string)json_encode($option));

        $data = [
            'links' => $this->links,
            'data' => $option,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
