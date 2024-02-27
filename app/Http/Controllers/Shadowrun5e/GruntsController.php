<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5e;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function array_key_exists;
use function array_keys;
use function array_values;
use function date;
use function json_encode;
use function sha1;
use function sha1_file;
use function stat;
use function strtolower;

/**
 * Controller for grunts.
 */
class GruntsController extends Controller
{
    /**
     * Filename for all of the data.
     */
    protected string $filename;

    /**
     * Collection of grunts.
     * @var array<string, mixed>
     */
    protected array $grunts;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'grunts.php';

        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        $this->grunts = require $this->filename;
    }

    /**
     * Get the entire collection of fifth edition grunts.
     */
    public function index(): Response
    {
        foreach (array_keys($this->grunts) as $key) {
            $this->grunts[$key]['links'] = [
                'self' => route('shadowrun5e.grunts.show', $key),
            ];
            $this->grunts[$key]['id'] = $key;
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $this->links['self'] = route('shadowrun5e.grunts.index');
        $data = [
            'links' => $this->links,
            'data' => array_values($this->grunts),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single fifth edition grunt.
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->grunts),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );
        $grunt = $this->grunts[$id];
        $grunt['links'] = [
            'self' => route('shadowrun5e.grunts.show', $id),
        ];
        $grunt['id'] = $id;

        $this->headers['Etag'] = sha1((string)json_encode($grunt));
        $this->links['collection'] = route('shadowrun5e.grunts.index');
        $this->links['self'] = route('shadowrun5e.grunts.show', $id);
        $data = [
            'links' => $this->links,
            'data' => $grunt,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}