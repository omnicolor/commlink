<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5e;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Controller for armor modifications.
 */
class ArmorModificationsController extends Controller
{
    /**
     * Filename for the data file.
     */
    protected string $filename;

    /**
     * Modifications.
     * @var array<string, mixed>
     */
    protected array $mods;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'armor-modifications.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/armor-modifications';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->mods = require $this->filename;
    }

    /**
     * Return the entire collection of armor modifications.
     */
    public function index(): Response
    {
        foreach (array_keys($this->mods) as $key) {
            $this->mods[$key]['links']['self'] = \sprintf(
                '/api/shadowrun5e/armor-modifications/%s',
                \urlencode($key)
            );
            $this->mods[$key]['ruleset'] ??= 'core';
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->mods),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single armor modification.
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->mods)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $mod = $this->mods[$id];
        $mod['links']['self'] = $this->links['self'] = \sprintf(
            '/api/shadowrun5e/armor-modifications/%s',
            \urlencode($id)
        );
        $mod['ruleset'] ??= 'core';
        $this->headers['Etag'] = \sha1((string)\json_encode($mod));

        $data = [
            'links' => $this->links,
            'data' => $mod,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
