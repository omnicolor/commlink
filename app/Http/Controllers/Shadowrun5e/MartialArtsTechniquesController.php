<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5e;

use Illuminate\Http\Response;

/**
 * Controller for Shadowrun 5th Edition Martial Arts Techniques.
 */
class MartialArtsTechniquesController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all techniques.
     * @var array<string, mixed>
     */
    protected array $techniques;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'martial-arts-techniques.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/martial-arts-techniques';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->techniques = require $this->filename;
    }

    /**
     * Get the entire collection of techniques.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->techniques as $key => $value) {
            $this->techniques[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/martial-arts-techniques/%s',
                    \urlencode($key)
                ),
            ];
        }
        $this->headers['Etag'] = \sha1_file($this->filename);
        $data = [
            'links' => $this->links,
            'data' => \array_values($this->techniques),
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return information about a single martial arts technique.
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->techniques)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $technique = $this->techniques[$id];
        $technique['links']['self'] = $this->links['self'] = \sprintf(
            '/api/shadowrun5e/martial-arts-techniques/%s',
            \urlencode($id)
        );

        $this->headers['Etag'] = \sha1((string)\json_encode($technique));
        $data = [
            'links' => $this->links,
            'data' => $technique,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
