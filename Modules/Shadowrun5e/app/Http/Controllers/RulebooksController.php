<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

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
 * Controller for Shadowrun rulebooks.
 * @psalm-suppress UnusedClass
 */
class RulebooksController extends Controller
{
    /**
     * Filename for all of the data.
     */
    protected string $filename;

    /**
     * Collection of rulebooks.
     * @var array<string, mixed>
     */
    protected array $rulebooks;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'rulebooks.php';

        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->rulebooks = require $this->filename;
    }

    /**
     * Get the entire collection of fifth edition rulebooks.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->rulebooks) as $key) {
            $this->rulebooks[$key]['links'] = [
                'self' => route('shadowrun5e.rulebooks.show', $key),
            ];
            $this->rulebooks[$key]['id'] = $key;
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $this->links['self'] = route('shadowrun5e.rulebooks.index');
        $data = [
            'links' => $this->links,
            'data' => array_values($this->rulebooks),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single fifth edition rulebook.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->rulebooks),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );
        $rulebook = $this->rulebooks[$id];
        $rulebook['links'] = [
            'self' => route('shadowrun5e.rulebooks.show', $id),
        ];
        $rulebook['id'] = $id;

        $this->headers['Etag'] = sha1((string)json_encode($rulebook));
        $this->links['collection'] = route('shadowrun5e.rulebooks.index');
        $this->links['self'] = route('shadowrun5e.rulebooks.show', $id);
        $data = [
            'links' => $this->links,
            'data' => $rulebook,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
