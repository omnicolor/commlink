<?php

declare(strict_types=1);

namespace Modules\Expanse\Http\Controllers;

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
use function sprintf;
use function stat;
use function strtolower;

class BackgroundsController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all backgrounds.
     * @var array<string, mixed>
     */
    protected array $backgrounds;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('expanse.data_path') . 'backgrounds.php';

        $this->backgrounds = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat);
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
    }

    public function index(): Response
    {
        foreach (array_keys($this->backgrounds) as $key) {
            $this->backgrounds[$key]['links'] = [
                'self' => route('expanse.backgrounds.show', ['background' => $key]),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $this->links['self'] = route('expanse.backgrounds.index');

        $data = [
            'links' => $this->links,
            'data' => array_values($this->backgrounds),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->backgrounds),
            Response::HTTP_NOT_FOUND,
            sprintf('%s not found', $id),
        );

        $this->links['collection'] = route('expanse.backgrounds.index');

        $background = $this->backgrounds[$id];
        $background['links']['self'] = $this->links['self'] =
            route('expanse.backgrounds.show', ['background' => $id]);

        $this->headers['Etag'] = sha1((string)json_encode($background));

        $data = [
            'links' => $this->links,
            'data' => $background,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
