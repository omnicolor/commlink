<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Controller for qualities.
 * @psalm-suppress UnusedClass
 */
class QualitiesController extends Controller
{
    /**
     * Filename for all of the data.
     */
    protected string $filename;

    /**
     * Collection of qualities.
     * @var array<string, mixed>
     */
    protected array $qualities;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e') . 'qualities.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/qualities';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->qualities = require $this->filename;
    }

    /**
     * Return a collection of qualities.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->qualities) as $key) {
            $this->qualities[$key]['links']['self'] = \sprintf(
                '/api/shadowrun5e/qualities/%s',
                \urlencode($key)
            );
            $this->qualities[$key]['ruleset'] ??= 'core';
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->qualities),
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Show a single Quality.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $qualityId): Response
    {
        $qualityId = \strtolower($qualityId);

        if (!\array_key_exists($qualityId, $this->qualities)) {
            // We couldn't find it!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => \sprintf('%s not found', $qualityId),
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $quality = $this->qualities[$qualityId];
        $quality['links']['self'] = $this->links['self'] =
            \sprintf('/api/shadowrun5e/qualities/%s', $qualityId);
        $quality['ruleset'] ??= 'core';

        $this->headers['Etag'] = \sha1((string)\json_encode($quality));

        $data = [
            'links' => $this->links,
            'data' => $quality,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
