<?php

declare(strict_types=1);

namespace App\Http\Controllers\Expanse;

use Illuminate\Http\Response;

/**
 * Controller for Expanse focuses.
 */
class FocusesController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all focuses.
     * @var array<string, mixed>
     */
    protected array $focuses;

    /**
     * Constructor.
     * @throws \ErrorException if the path to the data file is wrong
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.expanse') . 'focuses.php';
        $this->links['system'] = '/api/expanse';
        $this->links['collection'] = '/api/expanse/focuses';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->focuses = require $this->filename;
    }

    /**
     * Get the entire collection of Expanse focuses.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->focuses as $key => $unused) {
            $this->focuses[$key]['links'] = [
                'self' => \sprintf('/api/expanse/focuses/%s', $key),
            ];
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->focuses),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single Expanse focus.
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->focuses)) {
            // We couldn't find it!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => \sprintf('%s not found', $id),
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $focus = $this->focuses[$id];
        $focus['links']['self'] = $this->links['self'] =
            \sprintf('/api/expanse/focuses/%s', $id);

        $this->headers['Etag'] = \sha1((string)\json_encode($focus));

        $data = [
            'links' => $this->links,
            'data' => $focus,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
