<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Controller for weapon modifications.
 * @psalm-suppress UnusedClass
 */
class WeaponModificationsController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collectioin of all weapon modifications.
     * @var array<string, mixed>
     */
    protected array $mods;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'weapon-modifications.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/weapon-modifications';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->mods = require $this->filename;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->mods) as $key) {
            $this->mods[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/weapon-modifications/%s',
                    \urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->mods),
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
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
        $mod['ruleset'] ??= 'core';
        $mod['links']['self'] = $this->links['self']
            = \sprintf('/weapon-modifications/%s', \urlencode($id));

        $this->headers['Etag'] = \sha1((string)\json_encode($mod));

        $data = [
            'links' => $this->links,
            'data' => $mod,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
