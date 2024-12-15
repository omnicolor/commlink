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
 * Controller for Shadowrun 5th Edition martial arts styles.
 */
class MartialArtsStylesController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all styles.
     * @var array<string, mixed>
     */
    protected array $styles;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'martial-arts-styles.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/martial-arts-styles';

        $this->styles = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
    }

    /**
     * Get the entire collection of styles.
     */
    public function index(): Response
    {
        foreach (array_keys($this->styles) as $key) {
            $this->styles[$key]['links'] = [
                'self' => sprintf(
                    '/api/shadowrun5e/martial-arts-styles/%s',
                    urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->styles),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return information about a single style.
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        if (!array_key_exists($id, $this->styles)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $style = $this->styles[$id];
        $style['links']['self'] = $this->links['self'] = sprintf(
            '/api/shadowrun5e/martial-arts-styles/%s',
            urlencode($id)
        );

        $this->headers['Etag'] = sha1((string)json_encode($style));
        $data = [
            'links' => $this->links,
            'data' => $style,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
