<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5E;

use Illuminate\Http\Response;

/**
 * Mentor Spirit API route.
 */
class MentorSpiritsController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all mentor spirits.
     * @var array<string, mixed>
     */
    protected array $spirits;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'mentor-spirits.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/mentor-spirits';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->spirits = require $this->filename;
    }

    /**
     * Get the entire collection of Shadowrun 5E mentor spirits.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->spirits as $key => $unused) {
            $this->spirits[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/mentor-spirits/%s',
                    \urlencode($key)
                ),
            ];
            $this->spirits[$key]['ruleset'] ??= 'core';
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->spirits),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single mentor spirit.
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->spirits)) {
            // We couldn't find it!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $spirit = $this->spirits[$id];
        $spirit['ruleset'] ??= 'core';
        $spirit['links']['self'] = $this->links['self'] =
            \sprintf('/api/shadowrun5e/mentor-spirits/%s', \urlencode($id));

        $this->headers['Etag'] = \sha1((string)\json_encode($spirit));
        $data = [
            'links' => $this->links,
            'data' => $spirit,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
