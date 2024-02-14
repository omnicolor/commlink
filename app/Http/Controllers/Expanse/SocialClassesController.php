<?php

declare(strict_types=1);

namespace App\Http\Controllers\Expanse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function array_key_exists;
use function date;
use function json_encode;
use function sha1;
use function sha1_file;
use function sprintf;
use function stat;
use function strtolower;

/**
 * Controller for Expanse social classes.
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

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.expanse')
            . 'social-classes.php';
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        $this->classes = require $this->filename;
    }

    /**
     * Get the entire collection of Expanse social classes.
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
            'data' => $this->classes,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single Expanse social class.
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
