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
use function stat;
use function strtolower;

/**
 * Controller for intrusion countermeasures (ICE).
 * @psalm-suppress UnusedClass
 */
class IntrusionCountermeasuresController extends Controller
{
    /**
     * Filename for all of the data.
     */
    protected string $filename;

    /**
     * Collection of intrusion countermeasures.
     * @var array<string, mixed>
     */
    protected array $ice;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'intrusion-countermeasures.php';

        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->ice = require $this->filename;
    }

    /**
     * Get the entire collection of fifth edition ice.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->ice) as $key) {
            $this->ice[$key]['links'] = [
                'self' => route('shadowrun5e.intrusion-countermeasures.show', $key),
            ];
            $this->ice[$key]['id'] = $key;
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $this->links['self'] = route('shadowrun5e.intrusion-countermeasures.index');
        $data = [
            'links' => $this->links,
            'data' => array_values($this->ice),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single fifth edition ice.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->ice),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );
        $ice = $this->ice[$id];
        $ice['links'] = [
            'self' => route('shadowrun5e.intrusion-countermeasures.show', $id),
        ];
        $ice['id'] = $id;

        $this->headers['Etag'] = sha1((string)json_encode($ice));
        $this->links['collection'] = route('shadowrun5e.intrusion-countermeasures.index');
        $this->links['self'] = route('shadowrun5e.intrusion-countermeasures.show', $id);
        $data = [
            'links' => $this->links,
            'data' => $ice,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
