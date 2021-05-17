<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5E;

use Illuminate\Http\Response;

/**
 * Controller for Shadowrun 5E ammunition.
 */
class AmmunitionController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all ammunition.
     * @var array<string, mixed>
     */
    protected array $ammo;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'ammunition.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/ammunition';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->ammo = require $this->filename;
    }

    /**
     * Return a collection of ammunition resources.
     * @return Response
     */
    public function index(): Response
    {
        foreach ($this->ammo as $key => $value) {
            $this->ammo[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/ammunition/%s',
                    \urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->ammo),
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single ammuntion resource.
     * @param string $id ID of the ammunition to return
     * @return Response
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->ammo)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $ammo = $this->ammo[$id];
        $this->links['self'] = $ammo['links']['self'] = \sprintf(
            '/api/shadowrun5e/ammunition/%s',
            \urlencode($id)
        );
        $this->headers['Etag'] = \sha1((string)\json_encode($ammo));

        $data = [
            'links' => $this->links,
            'data' => $ammo,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
