<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function array_key_exists;
use function array_values;
use function assert;
use function date;
use function json_encode;
use function sha1;
use function sha1_file;
use function sprintf;
use function stat;
use function strtolower;
use function urlencode;

/**
 * Traditions route.
 */
class TraditionsController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all magical traditions.
     * @var array<string, mixed>
     */
    protected array $traditions;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path') . 'traditions.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/traditions';

        $this->traditions = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
    }

    /**
     * Get the entire collection.
     */
    public function index(): Response
    {
        foreach (array_keys($this->traditions) as $key) {
            $this->traditions[$key]['links'] = [
                'self' => sprintf(
                    '/api/shadowrun5e/traditions/%s',
                    urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->traditions),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single tradition.
     */
    public function show(string $identifier): Response
    {
        $identifier = strtolower($identifier);
        if (!array_key_exists($identifier, $this->traditions)) {
            // We couldn't find it!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => sprintf('%s not found', $identifier),
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $tradition = $this->traditions[$identifier];
        $tradition['links']['self'] = $this->links['self'] =
            sprintf('/api/shadowrun5e/traditions/%s', $identifier);

        $this->headers['Etag'] = sha1((string)json_encode($tradition));

        $data = [
            'links' => $this->links,
            'data' => $tradition,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
