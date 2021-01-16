<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5E;

use Illuminate\Http\Response;

/**
 * Controller for programs.
 */
class ProgramsController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all programs.
     * @var array<string, mixed>
     */
    protected array $programs;

    /**
     * Constructor.
     * @throws \ErrorException if the path to the data file is wrong
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_url') . 'programs.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/programs';
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        $this->programs = require $this->filename;
    }

    /**
     * Get the entire collection of Shadowrun programs.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->programs as $key => $value) {
            $this->programs[$key]['links'] = [
                'self' => sprintf('/api/shadowrun5e/programs/%s', $key),
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
     * @param string $programId
     * @return \Illuminate\Http\Response
     */
    public function show(string $programId): Response
    {
        if (!key_exists($programId, $this->programs)) {
            // We couldn't find it!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => sprintf('%s not found', $programId),
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $program = $this->programs[$programId];
        $program['links']['self'] = $this->links['self'] =
            sprintf('/programs/%s', urlencode($programId));

        $this->headers['Etag'] = sha1((string)json_encode($program));

        $data = [
            'links' => $this->links,
            'data' => $program,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
