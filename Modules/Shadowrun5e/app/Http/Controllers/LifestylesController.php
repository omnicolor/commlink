<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function abort_if;
use function array_key_exists;
use function array_keys;
use function array_values;
use function assert;
use function config;
use function date;
use function json_encode;
use function response;
use function route;
use function sha1;
use function sha1_file;
use function stat;
use function strtolower;

/**
 * Controller for Shadowrun 5th Edition lifestyles.
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

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'lifestyles.php';
        $this->links['collection'] = route('shadowrun5e.lifestyles.index');

        $this->lifestyles = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
    }

    /**
     * Return collection of all lifestyles.
     */
    public function index(): Response
    {
        foreach (array_keys($this->lifestyles) as $key) {
            $this->lifestyles[$key]['links'] = [
                'self' => route('shadowrun5e.lifestyles.show', $key),
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
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->lifestyles),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );

        $lifestyle = $this->lifestyles[$id];
        $this->links['self'] = $lifestyle['links']['self']
            = route('shadowrun5e.lifestyles.show', $id);
        $this->headers['Etag'] = sha1((string)json_encode($lifestyle));

        $data = [
            'links' => $this->links,
            'data' => $lifestyle,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
