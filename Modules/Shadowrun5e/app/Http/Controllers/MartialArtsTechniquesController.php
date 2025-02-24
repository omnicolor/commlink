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
 * Controller for Shadowrun 5th Edition Martial Arts Techniques.
 */
class MartialArtsTechniquesController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all techniques.
     * @var array<string, mixed>
     */
    protected array $techniques;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'martial-arts-techniques.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/martial-arts-techniques';

        $this->techniques = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
    }

    /**
     * Get the entire collection of techniques.
     */
    public function index(): Response
    {
        foreach (array_keys($this->techniques) as $key) {
            $this->techniques[$key]['links'] = [
                'self' => sprintf(
                    '/api/shadowrun5e/martial-arts-techniques/%s',
                    urlencode($key)
                ),
            ];
        }
        $this->headers['Etag'] = sha1_file($this->filename);
        $data = [
            'links' => $this->links,
            'data' => array_values($this->techniques),
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return information about a single martial arts technique.
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        if (!array_key_exists($id, $this->techniques)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $technique = $this->techniques[$id];
        $technique['links']['self'] = $this->links['self'] = sprintf(
            '/api/shadowrun5e/martial-arts-techniques/%s',
            urlencode($id)
        );

        $this->headers['Etag'] = sha1((string)json_encode($technique));
        $data = [
            'links' => $this->links,
            'data' => $technique,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
