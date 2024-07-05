<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Controller for programs.
 * @psalm-suppress UnusedClass
 */
class ProgramsController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all programs.
     * @var array<string, mixed>
     */
    protected array $programs;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e') . 'programs.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/programs';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        /** @psalm-suppress UnresolvableInclude */
        $this->programs = require $this->filename;
    }

    /**
     * Get the entire collection of Shadowrun programs.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        foreach (array_keys($this->programs) as $key) {
            $this->programs[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/programs/%s',
                    \urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->programs),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Show a single program.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $programId): Response
    {
        if (!\array_key_exists($programId, $this->programs)) {
            // We couldn't find it!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => \sprintf('%s not found', $programId),
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $program = $this->programs[$programId];
        $program['links']['self'] = $this->links['self'] =
            \sprintf('/programs/%s', \urlencode($programId));

        $this->headers['Etag'] = \sha1((string)\json_encode($program));

        $data = [
            'links' => $this->links,
            'data' => $program,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}