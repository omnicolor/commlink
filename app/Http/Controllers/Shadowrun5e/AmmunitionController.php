<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5e;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function array_key_exists;
use function array_keys;
use function date;
use function json_encode;
use function sha1;
use function sha1_file;
use function stat;

/**
 * Controller for Shadowrun 5E ammunition.
 */
class AmmunitionController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all ammunition.
     * @var array<string, mixed>
     */
    protected array $ammo;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'ammunition.php';
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        $this->ammo = require $this->filename;
    }

    /**
     * @param array<string, mixed> $ammo
     * @return array<string, mixed>
     */
    protected function cleanAmmo(array $ammo): array
    {
        $ammo['links'] = [
            'self' => route('shadowrun5e.ammunition.show', $ammo['id']),
        ];
        if (array_key_exists('ap-modifier', $ammo)) {
            $ammo['ap_modifier'] = $ammo['ap-modifier'];
            unset($ammo['ap-modifier']);
        }
        if (array_key_exists('damage-modifier', $ammo)) {
            $ammo['damage_modifier'] = $ammo['damage-modifier'];
            unset($ammo['damage-modifier']);
        }
        return $ammo;
    }

    /**
     * Return a collection of ammunition resources.
     */
    public function index(): Response
    {
        foreach (array_keys($this->ammo) as $key) {
            $this->ammo[$key] = $this->cleanAmmo($this->ammo[$key]);
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $this->links['self'] = route('shadowrun5e.ammunition.index');

        $data = [
            'links' => $this->links,
            'data' => $this->ammo,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single ammunition resource.
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->ammo),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );

        $ammo = $this->cleanAmmo($this->ammo[$id]);
        $this->links['collection'] = route('shadowrun5e.ammunition.index');
        $this->headers['Etag'] = sha1((string)json_encode($ammo));

        $data = [
            'links' => $this->links,
            'data' => $ammo,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
