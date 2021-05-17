<?php

declare(strict_types=1);

namespace App\Http\Controllers\Expanse;

use Illuminate\Http\Response;

/**
 * Controller for Expanse talents.
 */
class TalentsController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all talents.
     * @var array<string, mixed>
     */
    protected array $talents;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.expanse') . 'talents.php';
        $this->links['system'] = '/api/expanse';
        $this->links['collection'] = '/api/expanse/talents';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->talents = require $this->filename;
    }

    /**
     * Get the entire collection of Expanse talents.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->talents as $key => $unused) {
            $this->talents[$key]['links'] = [
                'self' => \sprintf('/api/expanse/talents/%s', $key),
            ];
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->talents),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single Expanse talent.
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->talents)) {
            // We couldn't find it!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => \sprintf('%s not found', $id),
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $talent = $this->talents[$id];
        $talent['links']['self'] = $this->links['self'] =
            \sprintf('/api/expanse/talents/%s', $id);

        $this->headers['Etag'] = \sha1((string)\json_encode($talent));

        $data = [
            'links' => $this->links,
            'data' => $talent,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
