<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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
 * Controller for Shadowrun 5E gear.
 */
class GearController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all gear.
     * @var array<string, mixed>
     */
    protected array $gear;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path') . 'gear.php';
        $this->links['collection'] = route('shadowrun5e.gear.index');

        $this->gear = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = Carbon::createFromTimestamp($stat['mtime'])->format('r');
    }

    /**
     * Get the entire collection of Shadowrun 5e gear.
     */
    public function index(): Response
    {
        /** @var User */
        $user = Auth::user();
        $trusted = $user->hasPermissionTo('view data');

        foreach (array_keys($this->gear) as $key) {
            $this->gear[$key]['links'] = [
                'self' => route('shadowrun5e.gear.show', $key),
            ];
            $this->gear[$key]['ruleset'] ??= 'core';
            $this->gear[$key]['effects'] = (object)($this->gear[$key]['effects'] ?? []);
            if (!$trusted) {
                unset($this->gear[$key]['description']);
            }
        }

        $data = [
            'links' => $this->links,
            'data' => array_values($this->gear),
        ];

        $this->headers['Etag'] = sha1_file($this->filename);
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single item.
     */
    public function show(string $id): Response
    {
        /** @var User */
        $user = Auth::user();

        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->gear),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );

        $item = $this->gear[$id];
        $item['ruleset'] ??= 'core';
        $item['links']['self'] = $this->links['self'] =
            route('shadowrun5e.gear.show', $id);

        if (!$user->hasPermissionTo('view data')) {
            unset($item['description']);
        }

        $data = [
            'links' => $this->links,
            'data' => $item,
        ];

        $this->headers['Etag'] = sha1((string)json_encode($item));
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
