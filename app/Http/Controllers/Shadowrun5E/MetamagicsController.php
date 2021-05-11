<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5E;

use Illuminate\Http\Response;

/**
 * Controller for metamagics.
 */
class MetamagicsController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all metamagics.
     * @var array<string, mixed>
     */
    protected array $magics;

    /**
     * Constructor.
     * @throws \ErrorException if the path to the data file is wrong
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'metamagics.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/metamagics';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->magics = require $this->filename;
    }

    /**
     * Return the entire collection of metamagics.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->magics as $key => $unused) {
            $this->magics[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/metamagics/%s',
                    \urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->magics),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single metamagic.
     * @param string $identifier
     * @return \Illuminate\Http\Response
     */
    public function show(string $identifier): Response
    {
        $identifier = \strtolower($identifier);
        if (!\array_key_exists($identifier, $this->magics)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => \sprintf('%s not found', $identifier),
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $magic = $this->magics[$identifier];
        $magic['links']['self'] = $this->links['self'] =
            \sprintf('/api/shadowrun5e/metamagics/%s', $identifier);

        $this->headers['Etag'] = \sha1((string)\json_encode($magic));

        $data = [
            'links' => $this->links,
            'data' => $magic,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
