<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5E;

use Illuminate\Http\Response;

/**
 * Controller for Shadowrun 5th Edition lifestyle options.
 */
class LifestyleOptionsController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all options.
     * @var array<string, mixed>
     */
    protected array $options;

    /**
     * Constructor.
     * @throws \ErrorException if the path to the data file is wrong
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'lifestyle-options.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/lifestyle-options';
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        $this->options = require $this->filename;
    }

    /**
     * Return collection of all lifestyle options.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->options as $key => $value) {
            $this->options[$key]['links'] = [
                'self' => sprintf(
                    '/api/shadowrun5e/lifestyle-options/%s',
                    urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->options),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single lifestyle option.
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        if (!array_key_exists($id, $this->options)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $option = $this->options[$id];
        $this->links['self'] = $option['links']['self']
            = sprintf('/api/shadowrun5e/lifestyle-options/%s', urlencode($id));
        $this->headers['Etag'] = sha1((string)json_encode($option));

        $data = [
            'links' => $this->links,
            'data' => $option,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
