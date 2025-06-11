<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function abort_if;
use function array_key_exists;
use function array_values;
use function assert;
use function config;
use function date;
use function json_encode;
use function response;
use function route;
use function sha1;
use function sha1_file;
use function stat;
use function strtolower;

/**
 * Controller for gear modifications.
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

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'gear-modifications.php';
        $this->links['collection'] = route('shadowrun5e.gear-modifications.index');

        $this->mods = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
    }

    /**
     * Return the entire collection of fifth edition gear modifications.
     */
    public function index(): Response
    {
        foreach ($this->mods as $key => $value) {
            $this->mods[$key]['links']['self'] = route(
                'shadowrun5e.gear-modifications.show',
                $key,
            );
            $this->mods[$key]['ruleset'] ??= 'core';
            $this->mods[$key]['effects']
                = (object)($this->mods[$key]['effects'] ?? []);
            $this->mods[$key]['wireless-effects']
                = (object)($this->mods[$key]['wireless-effects'] ?? []);
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->mods),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single fifth edition gear modification.
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->mods),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );

        $mod = $this->mods[$id];
        $mod['links']['self'] = $this->links['self'] =
            route('shadowrun5e.gear-modifications.show', $id);
        $mod['ruleset'] ??= 'core';

        $this->headers['Etag'] = sha1((string)json_encode($mod));

        $data = [
            'links' => $this->links,
            'data' => $mod,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
