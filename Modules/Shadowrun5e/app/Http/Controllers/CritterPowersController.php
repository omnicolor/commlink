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
 * Controller for critter powers.
 * @psalm-suppress UnusedClass
 */
class CritterPowersController extends Controller
{
    /**
     * Filename for all of the data.
     */
    protected string $filename;

    /**
     * Collection of critter powers.
     * @var array<string, mixed>
     */
    protected array $powers;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'critter-powers.php';

        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->powers = require $this->filename;
    }

    /**
     * Get the entire collection of fifth edition critter powers.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->powers) as $key) {
            $this->powers[$key]['links'] = [
                'self' => route('shadowrun5e.critter-powers.show', $key),
            ];
            $this->powers[$key]['id'] = $key;
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $this->links['self'] = route('shadowrun5e.critter-powers.index');
        $data = [
            'links' => $this->links,
            'data' => array_values($this->powers),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single fifth edition critter power.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->powers),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );
        $power = $this->powers[$id];
        $power['id'] = $id;
        $power['links'] = [
            'self' => route('shadowrun5e.critter-powers.show', $id),
        ];

        $this->headers['Etag'] = sha1((string)json_encode($power));
        $this->links['collection'] = route('shadowrun5e.critter-powers.index');
        $this->links['self'] = route('shadowrun5e.critter-powers.show', $id);
        $data = [
            'links' => $this->links,
            'data' => $power,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
