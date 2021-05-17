<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5E;

use Illuminate\Http\Response;

/**
 * Traditions route.
 */
class TraditionsController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all magical traditions.
     * @var array<string, mixed>
     */
    protected array $traditions;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'traditions.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/traditions';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->traditions = require $this->filename;
    }

    /**
     * Get the entire collection.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->traditions as $key => $unused) {
            $this->traditions[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/traditions/%s',
                    \urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->traditions),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single tradition.
     * @param string $identifier
     * @return \Illuminate\Http\Response
     */
    public function show(string $identifier): Response
    {
        $identifier = \strtolower($identifier);
        if (!\array_key_exists($identifier, $this->traditions)) {
            // We couldn't find it!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => \sprintf('%s not found', $identifier),
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $tradition = $this->traditions[$identifier];
        $tradition['links']['self'] = $this->links['self'] =
            \sprintf('/api/shadowrun5e/traditions/%s', $identifier);

        $this->headers['Etag'] = \sha1((string)\json_encode($tradition));

        $data = [
            'links' => $this->links,
            'data' => $tradition,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
