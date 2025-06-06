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
use function sprintf;
use function stat;

/**
 * Controller for programs.
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

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path') . 'programs.php';
        $this->links['collection'] = route('shadowrun5e.programs.index');

        $this->programs = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
    }

    /**
     * Get the entire collection of Shadowrun programs.
     */
    public function index(): Response
    {
        foreach (array_keys($this->programs) as $key) {
            $this->programs[$key]['links'] = [
                'self' => route('shadowrun5e.programs.show', $key),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->programs),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Show a single program.
     */
    public function show(string $programId): Response
    {
        abort_if(
            !array_key_exists($programId, $this->programs),
            Response::HTTP_NOT_FOUND,
            sprintf('%s not found', $programId),
        );

        $program = $this->programs[$programId];
        $program['links']['self'] = $this->links['self'] =
            route('shadowrun5e.programs.show', $programId);

        $this->headers['Etag'] = sha1((string)json_encode($program));

        $data = [
            'links' => $this->links,
            'data' => $program,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
