<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function abort_if;
use function array_key_exists;
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
        $this->links['collection'] = route('shadowrun5e.martial-arts-techniques.index');

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
                'self' => route('shadowrun5e.martial-arts-techniques.show', $key),
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
        abort_if(
            !array_key_exists($id, $this->techniques),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );

        $technique = $this->techniques[$id];
        $technique['links']['self'] = $this->links['self']
            = route('shadowrun5e.martial-arts-techniques.show', $id);

        $this->headers['Etag'] = sha1((string)json_encode($technique));
        $data = [
            'links' => $this->links,
            'data' => $technique,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
