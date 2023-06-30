<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5e;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Controller for weapons.
 */
class WeaponsController extends Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all weapons.
     * @var array<string, mixed>
     */
    protected array $weapons;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e') . 'weapons.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/weapons';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->weapons = require $this->filename;
    }

    /**
     * Return collection of all weapons.
     * @return Response
     */
    public function index(): Response
    {
        foreach (array_keys($this->weapons) as $key) {
            $this->weapons[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/weapons/%s',
                    \urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->weapons),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single weapon.
     * @param string $id
     * @return Response
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->weapons)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $weapon = $this->weapons[$id];
        $this->links['self'] = $weapon['links']['self']
            = \sprintf('/weapons/%s', \urlencode($id));
        $this->headers['Etag'] = \sha1((string)\json_encode($weapon));

        $data = [
            'links' => $this->links,
            'data' => $weapon,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
