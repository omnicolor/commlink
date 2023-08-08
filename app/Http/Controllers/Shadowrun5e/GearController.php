<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5e;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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
        $this->filename = config('app.data_path.shadowrun5e') . 'gear.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/gear';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->gear = require $this->filename;
    }

    /**
     * Get the entire collection of Shadowrun 5e gear.
     */
    public function index(): Response
    {
        $trusted = Auth::user()->hasPermissionTo('view data');
        foreach (array_keys($this->gear) as $key) {
            $this->gear[$key]['links'] = [
                'self' => \sprintf('/api/shadowrun5e/gear/%s', \urlencode($key)),
            ];
            $this->gear[$key]['ruleset'] ??= 'core';
            if (!$trusted) {
                unset($this->gear[$key]['description']);
            }
        }

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->gear),
        ];

        $this->headers['Etag'] = \sha1_file($this->filename);
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single item.
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->gear)) {
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
            \sprintf('/api/shadowrun5e/gear/%s', \urlencode($id));
        if (!Auth::user()->hasPermissionTo('view data')) {
            unset($item['description']);
        }

        $data = [
            'links' => $this->links,
            'data' => $item,
        ];

        $this->headers['Etag'] = \sha1((string)\json_encode($item));
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
