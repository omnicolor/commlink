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
 * Controller for Shadowrun armor.
 * @psalm-suppress UnusedClass
 */
class ArmorController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all armor.
     * @var array<string, mixed>
     */
    protected array $armor;

    /**
     * @param array<string, mixed> $armor
     * @return array<string, mixed>
     */
    protected function cleanArmor(array $armor): array
    {
        $armor['links'] = [
            'self' => route('shadowrun5e.armor.show', $armor['id']),
        ];
        if (array_key_exists('wireless-effects', $armor)) {
            $armor['wireless_effects'] = $armor['wireless-effects'];
            unset($armor['wireless-effects']);
        }
        if (array_key_exists('stack-rating', $armor)) {
            $armor['stack_rating'] = $armor['stack-rating'];
            unset($armor['stack-rating']);
        }
        $armor['ruleset'] ??= 'core';
        return $armor;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e') . 'armor.php';
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->armor = require $this->filename;
    }

    /**
     * Get the entire collection of Shadowrun 5E armor.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->armor) as $key) {
            $this->armor[$key] = $this->cleanArmor($this->armor[$key]);
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $this->links['collection'] = route('shadowrun5e.armor.index');

        $data = [
            'links' => $this->links,
            'data' => $this->armor,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single 5E armor.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->armor),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );

        $armor = $this->armor[$id];
        $this->links['self'] = route('shadowrun5e.armor.show', $id);
        $this->headers['Etag'] = sha1((string)json_encode($armor));

        $data = [
            'links' => $this->links,
            'data' => $armor,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
