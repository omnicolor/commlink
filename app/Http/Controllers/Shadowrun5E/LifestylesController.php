<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5E;

use Illuminate\Http\Response;

/**
 * Controller for Shadowrun 5th Edition lifestyles.
 */
class LifestylesController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all lifestyles.
     * @var array<string, mixed>
     */
    protected array $lifestyles;

    /**
     * Constructor.
     * @throws \ErrorException if the path to the data file is wrong
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'lifestyles.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/lifestyles';
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        $this->lifestyles = require $this->filename;
    }

    /**
     * Return collection of all lifestyles.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->lifestyles as $key => $value) {
            $this->lifestyles[$key]['links'] = [
                'self' => sprintf(
                    '/api/shadowrun5e/lifestyles/%s',
                    urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->lifestyles),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single lifestyle.
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        if (!key_exists($id, $this->lifestyles)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $lifestyle = $this->lifestyles[$id];
        $this->links['self'] = $lifestyle['links']['self']
            = sprintf('/api/shadowrun5e/lifestyles/%s', urlencode($id));
        $this->headers['Etag'] = sha1((string)json_encode($lifestyle));

        $data = [
            'links' => $this->links,
            'data' => $lifestyle,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
