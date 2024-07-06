<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function array_key_exists;
use function array_values;
use function config;
use function date;
use function json_encode;
use function route;
use function sha1;
use function sha1_file;
use function stat;
use function strtolower;

/**
 * Controller for weapons.
 * @psalm-suppress UnusedClass
 */
class WeaponsController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all weapons.
     * @var array<string, mixed>
     */
    protected array $weapons;

    /**
     * Constructor.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path') . 'weapons.php';
        $this->links['collection'] = route('shadowrun5e.weapons.index');
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->weapons = require $this->filename;
    }

    /**
     * Return collection of all weapons.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->weapons) as $key) {
            $this->weapons[$key]['links'] = [
                'self' => route('shadowrun5e.weapons.show', $key),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->weapons),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single weapon.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        if (!array_key_exists($id, $this->weapons)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $weapon = $this->weapons[$id];
        $this->links['self'] = $weapon['links']['self']
            = route('shadowrun5e.weapons.show', $id);
        $this->headers['Etag'] = sha1((string)json_encode($weapon));

        $data = [
            'links' => $this->links,
            'data' => $weapon,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
