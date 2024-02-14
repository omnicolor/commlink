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
 * Controller for Expanse talents.
 */
class TalentsController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all talents.
     * @var array<string, mixed>
     */
    protected array $talents;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.expanse') . 'talents.php';
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        $this->talents = require $this->filename;
    }

    /**
     * Get the entire collection of Expanse talents.
     */
    public function index(): Response
    {
        foreach (array_keys($this->talents) as $key) {
            $this->talents[$key]['links'] = [
                'self' => route('expanse.talents.show', $key),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $this->links['self'] = route('expanse.talents.index');

        $data = [
            'links' => $this->links,
            'data' => $this->talents,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single Expanse talent.
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->talents),
            Response::HTTP_NOT_FOUND,
            sprintf('%s not found', $id),
        );

        $talent = $this->talents[$id];
        $talent['links']['self'] = $this->links['self'] =
            route('expanse.talents.show', $id);

        $this->headers['Etag'] = sha1((string)json_encode($talent));
        $this->links['collection'] = route('expanse.talents.index');

        $data = [
            'links' => $this->links,
            'data' => $talent,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
