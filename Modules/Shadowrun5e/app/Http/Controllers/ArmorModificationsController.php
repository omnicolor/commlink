<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function array_key_exists;
use function array_keys;
use function date;
use function json_encode;
use function sha1;
use function sha1_file;
use function stat;
use function strtolower;

/**
 * Controller for armor modifications.
 * @psalm-suppress UnusedClass
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

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'armor-modifications.php';
        $this->links['collection'] = '/api/shadowrun5e/armor-modifications';
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->mods = require $this->filename;
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
        return $modification;
    }

    /**
     * Return the entire collection of armor modifications.
     * @psalm-suppress PossiblyUnusedMethod
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
            'data' => $this->mods,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single armor modification.
     * @psalm-suppress PossiblyUnusedMethod
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
