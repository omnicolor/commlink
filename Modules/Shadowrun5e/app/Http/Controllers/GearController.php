<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/gear';

        $this->gear = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
    }

    /**
     * Get the entire collection of Shadowrun 5e gear.
     */
    public function index(): Response
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        $trusted = $user->hasPermissionTo('view data');

        foreach (array_keys($this->gear) as $key) {
            $this->gear[$key]['links'] = [
                'self' => sprintf('/api/shadowrun5e/gear/%s', urlencode($key)),
            ];
            $this->gear[$key]['ruleset'] ??= 'core';
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
        /** @var \App\Models\User */
        $user = Auth::user();

        $id = strtolower($id);
        if (!array_key_exists($id, $this->gear)) {
            // We couldn't find it!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $item = $this->gear[$id];
        $item['ruleset'] ??= 'core';
        $item['links']['self'] = $this->links['self'] =
            sprintf('/api/shadowrun5e/gear/%s', urlencode($id));

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
