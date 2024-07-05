<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

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
 * Controller for critter weaknesses.
 * @psalm-suppress UnusedClass
 */
class CritterWeaknessesController extends Controller
{
    /**
     * Filename for all of the data.
     */
    protected string $filename;

    /**
     * Collection of critter weaknesses.
     * @var array<string, mixed>
     */
    protected array $weaknesses;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'critter-weaknesses.php';

        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->weaknesses = require $this->filename;
    }

    /**
     * Get the entire collection of fifth edition critter weaknesses.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->weaknesses) as $key) {
            $this->weaknesses[$key]['links'] = [
                'self' => route('shadowrun5e.critter-weaknesses.show', $key),
            ];
            $this->weaknesses[$key]['id'] = $key;
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $this->links['self'] = route('shadowrun5e.critter-weaknesses.index');
        $data = [
            'links' => $this->links,
            'data' => array_values($this->weaknesses),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single fifth edition critter weaknesses.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->weaknesses),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );
        $weakness = $this->weaknesses[$id];
        $weakness['id'] = $id;
        $weakness['links'] = [
            'self' => route('shadowrun5e.critter-weaknesses.show', $id),
        ];

        $this->headers['Etag'] = sha1((string)json_encode($weakness));
        $this->links['collection'] = route('shadowrun5e.critter-weaknesses.index');
        $this->links['self'] = route('shadowrun5e.critter-weaknesses.show', $id);
        $data = [
            'links' => $this->links,
            'data' => $weakness,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
