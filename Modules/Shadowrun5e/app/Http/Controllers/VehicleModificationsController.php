<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Vehicle modifications for Shadowrun 5E controller.
 * @psalm-suppress UnusedClass
 */
class VehicleModificationsController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all vehicle modifications.
     * @var array<string, mixed>
     */
    protected array $mods;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'vehicle-modifications.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/vehicle-modifications';

        /** @psalm-suppress UnresolvableInclude */
        $this->mods = require $this->filename;

        $stat = \stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
    }

    /**
     * Return a collection of all modifications.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->mods) as $key) {
            $this->mods[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/vehicle-modifications/%s',
                    \urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = \sha1_file($this->filename);
        $data = [
            'links' => $this->links,
            'data' => \array_values($this->mods),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single vehicle modification.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->mods)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $mod = $this->mods[$id];
        $this->links['self'] = $mod['links']['self'] = \sprintf(
            '/api/shadowrun5e/vehicle-modifications/%s',
            \urlencode($id)
        );
        $this->headers['Etag'] = \sha1((string)\json_encode($mod));
        $data = [
            'links' => $this->links,
            'data' => $mod,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
