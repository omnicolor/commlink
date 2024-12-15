<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function array_key_exists;
use function array_values;
use function date;
use function json_encode;
use function sha1;
use function sha1_file;
use function sprintf;
use function stat;
use function strtolower;
use function urlencode;

/**
 * Controller for metamagics.
 */
class MetamagicsController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all metamagics.
     * @var array<string, mixed>
     */
    protected array $magics;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'metamagics.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/metamagics';

        $this->magics = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
    }

    /**
     * Return the entire collection of metamagics.
     */
    public function index(): Response
    {
        foreach (array_keys($this->magics) as $key) {
            $this->magics[$key]['links'] = [
                'self' => sprintf(
                    '/api/shadowrun5e/metamagics/%s',
                    urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->magics),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single metamagic.
     */
    public function show(string $identifier): Response
    {
        $identifier = strtolower($identifier);
        if (!array_key_exists($identifier, $this->magics)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => sprintf('%s not found', $identifier),
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $magic = $this->magics[$identifier];
        $magic['links']['self'] = $this->links['self'] =
            sprintf('/api/shadowrun5e/metamagics/%s', $identifier);

        $this->headers['Etag'] = sha1((string)json_encode($magic));

        $data = [
            'links' => $this->links,
            'data' => $magic,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
