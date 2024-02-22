<?php

declare(strict_types=1);

namespace App\Http\Controllers\Expanse;

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
 * Controller for Expanse focuses.
 */
class FocusesController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all focuses.
     * @var array<string, mixed>
     */
    protected array $focuses;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.expanse') . 'focuses.php';
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        $this->focuses = require $this->filename;
    }

    /**
     * Get the entire collection of Expanse focuses.
     */
    public function index(): Response
    {
        foreach (array_keys($this->focuses) as $key) {
            $this->focuses[$key]['links'] = [
                'self' => route('expanse.focuses.show', ['focus' => $key]),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $this->links['self'] = route('expanse.focuses.index');

        $data = [
            'links' => $this->links,
            'data' => array_values($this->focuses),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single Expanse focus.
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->focuses),
            Response::HTTP_NOT_FOUND,
            sprintf('%s not found', $id),
        );

        $focus = $this->focuses[$id];
        $focus['links']['self'] = $this->links['self'] =
            route('expanse.focuses.show', ['focus' => $id]);

        $this->headers['Etag'] = sha1((string)json_encode($focus));
        $this->links['collection'] = route('expanse.focuses.index');

        $data = [
            'links' => $this->links,
            'data' => $focus,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
