<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function array_key_exists;
use function array_keys;
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
 * Controller for Shadowrun 5th Edition lifestyles.
 * @psalm-suppress UnusedClass
 */
class LifestylesController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all lifestyles.
     * @var array<string, mixed>
     */
    protected array $lifestyles;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'lifestyles.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/lifestyles';
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->lifestyles = require $this->filename;
    }

    /**
     * Return collection of all lifestyles.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->lifestyles) as $key) {
            $this->lifestyles[$key]['links'] = [
                'self' => sprintf(
                    '/api/shadowrun5e/lifestyles/%s',
                    urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->lifestyles),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single lifestyle.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        if (!array_key_exists($id, $this->lifestyles)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $lifestyle = $this->lifestyles[$id];
        $this->links['self'] = $lifestyle['links']['self']
            = sprintf('/api/shadowrun5e/lifestyles/%s', urlencode($id));
        $this->headers['Etag'] = sha1((string)json_encode($lifestyle));

        $data = [
            'links' => $this->links,
            'data' => $lifestyle,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
