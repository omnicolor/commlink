<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5E;

use Illuminate\Http\Response;

/**
 * Vehicle modifications for Shadowrun 5E controller.
 */
class VehicleModificationsController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all vehicle modifications.
     * @var array<string, mixed>
     */
    protected array $mods;

    /**
     * Constructor.
     * @throws \ErrorException if the path to the data file is wrong.
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'vehicle-modifications.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/vehicle-modifications';
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        $this->mods = require $this->filename;
    }

    /**
     * Return a collection of all modifications.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->mods as $key => $value) {
            $this->mods[$key]['links'] = [
                'self' => sprintf(
                    '/api/shadowrun5e/vehicle-modifications/%s',
                    urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $data = [
            'links' => $this->links,
            'data' => array_values($this->mods),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single vehicle modification.
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        if (!array_key_exists($id, $this->mods)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $mod = $this->mods[$id];
        $this->links['self'] = $mod['links']['self'] = sprintf(
            '/api/shadowrun5e/vehicle-modifications/%s',
            urlencode($id)
        );
        $this->headers['Etag'] = sha1((string)json_encode($mod));
        $data = [
            'links' => $this->links,
            'data' => $mod,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
