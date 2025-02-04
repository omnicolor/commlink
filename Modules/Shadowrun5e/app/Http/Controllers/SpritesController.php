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
 * Controller for technomancer sprites.
 */
class SpritesController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all sprites.
     * @var array<string, mixed>
     */
    protected array $sprites;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path') . 'sprites.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/sprites';

        $this->sprites = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
    }

    public function index(): Response
    {
        foreach (array_keys($this->sprites) as $key) {
            $this->sprites[$key]['links'] = [
                'self' => sprintf(
                    '/api/shadowrun5e/sprites/%s',
                    urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->sprites),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    public function show(string $identifier): Response
    {
        $identifier = strtolower($identifier);
        if (!array_key_exists($identifier, $this->sprites)) {
            // We couldn't find it!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => sprintf('%s not found', $identifier),
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $sprite = $this->sprites[$identifier];
        $sprite['links']['self'] = $this->links['self'] =
            sprintf('/api/shadowrun5e/sprites/%s', $identifier);

        $this->headers['Etag'] = sha1((string)json_encode($sprite));

        $data = [
            'links' => $this->links,
            'data' => $sprite,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
