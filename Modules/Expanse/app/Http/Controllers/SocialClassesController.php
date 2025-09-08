<?php

declare(strict_types=1);

namespace Modules\Expanse\Http\Controllers;

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
use function sprintf;
use function stat;
use function strtolower;

class SocialClassesController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all social classes.
     * @var array<string, mixed>
     */
    protected array $classes;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('expanse.data_path') . 'social-classes.php';

        $this->classes = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat);
        $this->headers['Last-Modified'] = Carbon::createFromTimestamp($stat['mtime'])->format('r');
    }

    public function index(): Response
    {
        foreach (array_keys($this->classes) as $key) {
            $this->classes[$key]['links'] = [
                'self' => route('expanse.social-classes.show', $key),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $this->links['self'] = route('expanse.social-classes.index');

        $data = [
            'links' => $this->links,
            'data' => array_values($this->classes),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->classes),
            Response::HTTP_NOT_FOUND,
            sprintf('%s not found', $id),
        );

        $class = $this->classes[$id];
        $class['links']['self'] = $this->links['self'] =
            route('expanse.social-classes.show', $id);

        $this->headers['Etag'] = sha1((string)json_encode($class));
        $this->links['collection'] = route('expanse.social-classes.index');

        $data = [
            'links' => $this->links,
            'data' => $class,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
