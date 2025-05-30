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
use function stat;
use function strtolower;

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
        $this->filename = config('shadowrun5e.data_path')
            . 'armor-modifications.php';

        $this->mods = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
    }

    /**
     * @param array<string, mixed> $modification
     * @return array<string, mixed>
     */
    protected function cleanModification(array $modification): array
    {
        $modification['links']['self'] = route(
            'shadowrun5e.armor-modifications.show',
            $modification['id'],
        );
        if (array_key_exists('capacity-cost', $modification)) {
            $modification['capacity_cost'] = $modification['capacity-cost'];
            unset($modification['capacity-cost']);
        }
        if (array_key_exists('wireless-effects', $modification)) {
            $modification['wireless_effects'] = $modification['wireless-effects'];
            unset($modification['wireless-effects']);
        }
        $modification['ruleset'] ??= 'core';
        $modification['effects'] = (object)($modification['effects'] ?? []);
        return $modification;
    }

    /**
     * Return the entire collection of armor modifications.
     */
    public function index(): Response
    {
        foreach (array_keys($this->mods) as $key) {
            $this->mods[$key] = $this->cleanModification($this->mods[$key]);
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $this->links['self'] = route('shadowrun5e.armor-modifications.index');

        $data = [
            'links' => $this->links,
            'data' => array_values($this->mods),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single armor modification.
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->mods),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );

        $mod = $this->cleanModification($this->mods[$id]);
        $this->headers['Etag'] = sha1((string)json_encode($mod));
        $this->links['collection']
            = route('shadowrun5e.armor-modifications.index');

        $data = [
            'links' => $this->links,
            'data' => $mod,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
