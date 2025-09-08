<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Response;

use function abort_if;
use function array_key_exists;
use function array_keys;
use function array_values;
use function assert;
use function config;
use function json_encode;
use function response;
use function route;
use function sha1;
use function sha1_file;
use function stat;

/**
 * Controller for the various Shadowrun skill requests.
 */
class SkillsController extends Controller
{
    /**
     * Filename for all of the skills.
     */
    protected string $filename;

    /**
     * Collection of skills.
     * @var array<string, mixed>
     */
    protected array $skills;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path') . 'skills.php';
        $this->links['collection'] = route('shadowrun5e.skills.index');

        $this->skills = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = Carbon::createFromTimestamp($stat['mtime'])->format('r');
    }

    /**
     * Return all skills.
     */
    public function index(): Response
    {
        foreach (array_keys($this->skills) as $key) {
            $this->skills[$key]['links'] = [
                'self' => route('shadowrun5e.skills.show', $key),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $data = [
            'links' => $this->links,
            'data' => array_values($this->skills),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single skill.
     */
    public function show(string $id): Response
    {
        abort_if(
            !array_key_exists($id, $this->skills),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );

        $skill = $this->skills[$id];
        $skill['links']['self'] = $this->links['self'] =
            route('shadowrun5e.skills.show', $id);

        $this->headers['Etag'] = sha1((string)json_encode($skill));
        $data = [
            'links' => $this->links,
            'data' => $skill,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
