<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5E;

use Illuminate\Http\Response;

/**
 * Controller for Shadowrun 5th Edition martial arts styles.
 */
class MartialArtsStylesController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all styles.
     * @var array<string, mixed>
     */
    protected array $styles;

    /**
     * Constructor.
     * @throws \ErrorException if the path to the data file is wrong
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_url') . 'martial-arts-styles.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/martial-arts-styles';
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        $this->styles = require $this->filename;
    }

    /**
     * Get the entire collection of styles.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->styles as $key => $value) {
            $this->styles[$key]['links'] = [
                'self' => sprintf(
                    '/api/shadowrun5e/martial-arts-styles/%s',
                    $key
                ),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->styles),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return information about a single style.
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        if (!key_exists($id, $this->styles)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $style = $this->styles[$id];
        $style['links']['self'] = $this->links['self'] = sprintf(
            '/api/shadowrun5e/martial-arts-styles/%s',
            urlencode($id)
        );

        $this->headers['Etag'] = sha1((string)json_encode($style));
        $data = [
            'links' => $this->links,
            'data' => $style,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
