<?php

declare(strict_types=1);

namespace App\Http\Controllers\Expanse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function array_key_exists;
use function array_values;
use function date;
use function json_encode;
use function sha1;
use function sha1_file;
use function stat;
use function strtolower;

/**
 * Controller for Expanse backgrounds.
 */
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
        $this->filename = config('app.data_path.expanse') . 'backgrounds.php';
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        $this->backgrounds = require $this->filename;
    }

    /**
     * Get the entire collection of Expanse backgrounds.
     */
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

    /**
     * Get a single Expanse background.
     */
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
