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
 * Controller for Shadowrun 5th Edition lifestyle options.
 */
class LifestyleOptionsController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all options.
     * @var array<string, mixed>
     */
    protected array $options;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'lifestyle-options.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/lifestyle-options';

        $this->options = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
    }

    /**
     * Return collection of all lifestyle options.
     */
    public function index(): Response
    {
        foreach (array_keys($this->options) as $key) {
            $this->options[$key]['links'] = [
                'self' => sprintf(
                    '/api/shadowrun5e/lifestyle-options/%s',
                    urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->options),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single lifestyle option.
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        if (!array_key_exists($id, $this->options)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $option = $this->options[$id];
        $this->links['self'] = $option['links']['self']
            = sprintf('/api/shadowrun5e/lifestyle-options/%s', urlencode($id));
        $this->headers['Etag'] = sha1((string)json_encode($option));

        $data = [
            'links' => $this->links,
            'data' => $option,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
