<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function abort_if;
use function array_key_exists;
use function array_values;
use function assert;
use function config;
use function date;
use function json_encode;
use function response;
use function route;
use function sha1;
use function sha1_file;
use function stat;
use function strtolower;

/**
 * Mentor Spirit API route.
 */
class MentorSpiritsController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all mentor spirits.
     * @var array<string, mixed>
     */
    protected array $spirits;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'mentor-spirits.php';
        $this->links['collection'] = route('shadowrun5e.mentor-spirits.index');

        $this->spirits = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
    }

    /**
     * Get the entire collection of Shadowrun 5E mentor spirits.
     */
    public function index(): Response
    {
        foreach (array_keys($this->spirits) as $key) {
            $this->spirits[$key]['links'] = [
                'self' => route('shadowrun5e.mentor-spirits.show', $key),
            ];
            $this->spirits[$key]['ruleset'] ??= 'core';
            $this->spirits[$key]['effects'] = (object)($this->spirits[$key]['effects'] ?? []);
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->spirits),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single mentor spirit.
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->spirits),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );

        $spirit = $this->spirits[$id];
        $spirit['ruleset'] ??= 'core';
        $spirit['links']['self'] = $this->links['self'] =
            route('shadowrun5e.mentor-spirits.show', $id);

        $this->headers['Etag'] = sha1((string)json_encode($spirit));
        $data = [
            'links' => $this->links,
            'data' => $spirit,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
