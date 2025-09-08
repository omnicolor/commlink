<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Response;

use function array_key_exists;
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
 * Controller for Shadowrun 5th Edition lifestyle zones.
 */
class LifestyleZonesController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all zones.
     * @var array<string, mixed>
     */
    protected array $zones;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'lifestyle-zones.php';
        $this->links['collection'] = route('shadowrun5e.lifestyle-zones.index');

        $this->zones = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = Carbon::createFromTimestamp($stat['mtime'])->format('r');
    }

    /**
     * Return collection of all lifestyle zones.
     */
    public function index(): Response
    {
        foreach (array_keys($this->zones) as $key) {
            $this->zones[$key]['links'] = [
                'self' => route('shadowrun5e.lifestyle-zones.show', $key),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->zones),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single lifestyle zones.
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->zones),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );

        $zone = $this->zones[$id];
        $this->links['self'] = $zone['links']['self']
            = route('shadowrun5e.lifestyle-zones.show', $id);
        $this->headers['Etag'] = sha1((string)json_encode($zone));

        $data = [
            'links' => $this->links,
            'data' => $zone,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
