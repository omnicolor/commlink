<?php

declare(strict_types=1);

namespace Modules\Expanse\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function array_key_exists;
use function array_keys;
use function array_values;
use function date;
use function json_encode;
use function sha1;
use function sha1_file;
use function sprintf;
use function stat;
use function strtolower;

/**
 * @psalm-suppress UnusedClass
 */
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

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('expanse.data_path') . 'social-classes.php';
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->classes = require $this->filename;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
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

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
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
