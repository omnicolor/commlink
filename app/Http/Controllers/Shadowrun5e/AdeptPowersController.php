<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5e;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function array_key_exists;
use function array_keys;
use function array_values;
use function date;
use function json_encode;
use function sha1;
use function sha1_file;
use function stat;
use function strtolower;

/**
 * Controller for adept powers.
 */
class AdeptPowersController extends Controller
{
    /**
     * Filename for all of the data.
     */
    protected string $filename;

    /**
     * Collection of adept powers.
     * @var array<string, mixed>
     */
    protected array $powers;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'adept-powers.php';

        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        $this->powers = require $this->filename;
    }

    /**
     * @param array<string, mixed> $power
     * @return array<string, mixed>
     */
    protected function cleanup(array $power): array
    {
        if (array_key_exists('incompatible-with', $power)) {
            $power['incompatible_with'] = $power['incompatible-with'];
            unset($power['incompatible-with']);
        }
        $power['links'] = [
            'self' => route('shadowrun5e.adept-powers.show', ['adept_power' => $power['id']]),
        ];

        if (array_key_exists('effects', $power) && 0 !== count($power['effects'])) {
            $effects = [];
            /** @var string $key */
            foreach ($power['effects'] as $key => $effect) {
                $effects[str_replace('-', '_', $key)] = $effect;
            }
            $power['effects'] = $effects;
        }
        return $power;
    }

    /**
     * Get the entire collection of fifth edition adept powers.
     */
    public function index(): Response
    {
        foreach (array_keys($this->powers) as $key) {
            $this->powers[$key] = $this->cleanup($this->powers[$key]);
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $this->links['self'] = route('shadowrun5e.adept-powers.index');
        $data = [
            'links' => $this->links,
            'data' => array_values($this->powers),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single fifth edition adept power.
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        if (!array_key_exists($id, $this->powers)) {
            // We couldn't find it!
            $errors = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($errors);
        }
        $power = $this->cleanup($this->powers[$id]);

        $this->headers['Etag'] = sha1((string)json_encode($power));
        $this->links['collection'] = route('shadowrun5e.adept-powers.index');
        $this->links['self'] = route('shadowrun5e.adept-powers.show', ['adept_power' => $id]);
        $data = [
            'links' => $this->links,
            'data' => $power,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
