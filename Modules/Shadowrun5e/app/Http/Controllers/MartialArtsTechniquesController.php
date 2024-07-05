<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Controller for Shadowrun 5th Edition Martial Arts Techniques.
 * @psalm-suppress UnusedClass
 */
class MartialArtsTechniquesController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all techniques.
     * @var array<string, mixed>
     */
    protected array $techniques;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'martial-arts-techniques.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/martial-arts-techniques';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->techniques = require $this->filename;
    }

    /**
     * Get the entire collection of techniques.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->techniques) as $key) {
            $this->techniques[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/martial-arts-techniques/%s',
                    \urlencode($key)
                ),
            ];
        }
        $this->headers['Etag'] = \sha1_file($this->filename);
        $data = [
            'links' => $this->links,
            'data' => \array_values($this->techniques),
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return information about a single martial arts technique.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->techniques)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $technique = $this->techniques[$id];
        $technique['links']['self'] = $this->links['self'] = \sprintf(
            '/api/shadowrun5e/martial-arts-techniques/%s',
            \urlencode($id)
        );

        $this->headers['Etag'] = \sha1((string)\json_encode($technique));
        $data = [
            'links' => $this->links,
            'data' => $technique,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
