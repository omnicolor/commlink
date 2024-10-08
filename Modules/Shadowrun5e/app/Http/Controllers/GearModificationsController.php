<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Controller for gear modifications.
 * @psalm-suppress UnusedClass
 */
class GearModificationsController extends Controller
{
    /**
     * Filename for the data file.
     */
    protected string $filename;

    /**
     * Collection of gear modifications.
     * @var array<string, mixed>
     */
    protected array $mods;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'gear-modifications.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/gear-modifications';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->mods = require $this->filename;
    }

    /**
     * Return the entire collection of fifth edition gear modifications.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach ($this->mods as $key => $value) {
            $this->mods[$key]['links']['self'] = \sprintf(
                '/api/shadowrun5e/gear-modifications/%s',
                \urlencode($key)
            );
            if (!\array_key_exists('ruleset', $value)) {
                $this->mods[$key]['ruleset'] = 'core';
            }
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->mods),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single fifth edition gear modification.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->mods)) {
            // We couldn't find it!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $mod = $this->mods[$id];
        $mod['links']['self'] = $this->links['self'] =
            \sprintf('/api/shadowrun5e/gear-modifications/%s', $id);
        $mod['ruleset'] ??= 'core';

        $this->headers['Etag'] = \sha1((string)\json_encode($mod));

        $data = [
            'links' => $this->links,
            'data' => $mod,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
