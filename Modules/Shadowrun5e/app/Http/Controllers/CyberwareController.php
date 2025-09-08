<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Response;

use function abort_if;
use function array_key_exists;
use function array_keys;
use function array_values;
use function assert;
use function config;
use function json_encode;
use function response;
use function route;
use function sha1;
use function sha1_file;
use function stat;
use function strtolower;

/**
 * Controller for Shadowrun augmentations.
 */
class CyberwareController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all augmentations.
     * @var array<string, mixed>
     */
    protected array $augmentations;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path') . 'cyberware.php';
        $this->links['collection'] = route('shadowrun5e.cyberware.index');

        $this->augmentations = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = Carbon::createFromTimestamp($stat['mtime'])->format('r');
    }

    /**
     * Get the entire collection of Shadowrun 5E augmentations.
     */
    public function index(): Response
    {
        foreach (array_keys($this->augmentations) as $key) {
            $this->augmentations[$key]['links'] = [
                'self' => route('shadowrun5e.cyberware.show', $key),
            ];
            $this->augmentations[$key]['ruleset'] ??= 'core';
            $this->augmentations[$key]['effects']
                = (object)($this->augmentations[$key]['effects'] ?? []);
            $this->augmentations[$key]['wireless-effects']
                = (object)($this->augmentations[$key]['wireless-effects'] ?? []);
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->augmentations),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single Shadowrun 5E augmentation.
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->augmentations),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );

        $cyberware = $this->augmentations[$id];
        $cyberware['ruleset'] ??= 'core';
        $cyberware['links']['self'] = $this->links['self'] =
            route('shadowrun5e.cyberware.show', $id);

        $this->headers['Etag'] = sha1((string)json_encode($cyberware));

        // Return the single item.
        $data = [
            'links' => $this->links,
            'data' => $cyberware,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
