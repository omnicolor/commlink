<?php

declare(strict_types=1);

namespace App\Http\Controllers\Expanse;

use Illuminate\Http\Response;

/**
 * Controller for Expanse backgrounds.
 */
class BackgroundsController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all backgrounds.
     * @var array<string, mixed>
     */
    protected array $backgrounds;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.expanse') . 'backgrounds.php';
        $this->links['system'] = '/api/expanse';
        $this->links['collection'] = '/api/expanse/backgrounds';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->backgrounds = require $this->filename;
    }

    /**
     * Get the entire collection of Expanse backgrounds.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->backgrounds as $key => $unused) {
            $this->backgrounds[$key]['links'] = [
                'self' => \sprintf('/api/expanse/backgrounds/%s', $key),
            ];
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->backgrounds),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single Expanse background.
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->backgrounds)) {
            // We couldn't find it!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => \sprintf('%s not found', $id),
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $background = $this->backgrounds[$id];
        $background['links']['self'] = $this->links['self'] =
            \sprintf('/api/expanse/backgrounds/%s', $id);

        $this->headers['Etag'] = \sha1((string)\json_encode($background));

        $data = [
            'links' => $this->links,
            'data' => $background,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
