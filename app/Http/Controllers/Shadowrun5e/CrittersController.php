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
 * Controller for critters.
 */
class CrittersController extends Controller
{
    /**
     * Filename for all of the data.
     */
    protected string $filename;

    /**
     * Collection of critters.
     * @var array<string, mixed>
     */
    protected array $critters;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'critters.php';

        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        $this->critters = require $this->filename;
    }

    /**
     * Get the entire collection of fifth edition critters.
     */
    public function index(): Response
    {
        foreach (array_keys($this->critters) as $key) {
            $this->critters[$key]['links'] = [
                'self' => route('shadowrun5e.critters.show', $key),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $this->links['self'] = route('shadowrun5e.critters.index');
        $data = [
            'links' => $this->links,
            'data' => array_values($this->critters),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single fifth edition critter.
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->critters),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );
        $critter = $this->critters[$id];
        $critter['links'] = [
            'self' => route('shadowrun5e.critters.show', $id),
        ];

        $this->headers['Etag'] = sha1((string)json_encode($critter));
        $this->links['collection'] = route('shadowrun5e.critters.index');
        $this->links['self'] = route('shadowrun5e.critters.show', $id);
        $data = [
            'links' => $this->links,
            'data' => $critter,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
