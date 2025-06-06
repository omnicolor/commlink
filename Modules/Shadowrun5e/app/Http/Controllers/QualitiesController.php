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
use function sprintf;
use function stat;
use function strtolower;

/**
 * Controller for qualities.
 */
class QualitiesController extends Controller
{
    /**
     * Filename for all of the data.
     */
    protected string $filename;

    /**
     * Collection of qualities.
     * @var array<string, mixed>
     */
    protected array $qualities;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path') . 'qualities.php';
        $this->links['collection'] = route('shadowrun5e.qualities.index');

        $this->qualities = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
    }

    /**
     * Return a collection of qualities.
     */
    public function index(): Response
    {
        foreach (array_keys($this->qualities) as $key) {
            $this->qualities[$key]['links']['self'] =
                route('shadowrun5e.qualities.show', $key);
            $this->qualities[$key]['ruleset'] ??= 'core';
            $this->qualities[$key]['effects'] = (object)($this->qualities[$key]['effects'] ?? []);
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->qualities),
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Show a single Quality.
     */
    public function show(string $qualityId): Response
    {
        $qualityId = strtolower($qualityId);
        abort_if(
            !array_key_exists($qualityId, $this->qualities),
            Response::HTTP_NOT_FOUND,
            sprintf('%s not found', $qualityId),
        );

        $quality = $this->qualities[$qualityId];
        $quality['links']['self'] = $this->links['self'] =
            route('shadowrun5e.qualities.show', $qualityId);
        $quality['ruleset'] ??= 'core';

        $this->headers['Etag'] = sha1((string)json_encode($quality));

        $data = [
            'links' => $this->links,
            'data' => $quality,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
