<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Response;

use function array_key_exists;
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
 * Controller for Shadowrun 5th Edition martial arts styles.
 */
class MartialArtsStylesController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all styles.
     * @var array<string, mixed>
     */
    protected array $styles;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'martial-arts-styles.php';
        $this->links['collection'] = route('shadowrun5e.martial-arts-styles.index');

        $this->styles = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = Carbon::createFromTimestamp($stat['mtime'])->format('r');
    }

    public function index(): Response
    {
        foreach (array_keys($this->styles) as $key) {
            $this->styles[$key]['links'] = [
                'self' => route('shadowrun5e.martial-arts-styles.show', $key),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->styles),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return information about a single style.
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->styles),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );

        $style = $this->styles[$id];
        $style['links']['self'] = $this->links['self']
            = route('shadowrun5e.martial-arts-styles.show', $id);

        $this->headers['Etag'] = sha1((string)json_encode($style));
        $data = [
            'links' => $this->links,
            'data' => $style,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
