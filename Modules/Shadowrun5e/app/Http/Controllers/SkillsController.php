<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function array_key_exists;
use function array_values;
use function assert;
use function date;
use function json_encode;
use function sha1;
use function sha1_file;
use function sprintf;
use function stat;
use function urlencode;

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
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/skills';

        $this->skills = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
    }

    /**
     * Return all skills.
     */
    public function index(): Response
    {
        foreach (array_keys($this->skills) as $key) {
            $this->skills[$key]['links'] = [
                'self' => sprintf(
                    '/api/shadowrun5e/skills/%s',
                    urlencode($key)
                ),
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
        if (!array_key_exists($id, $this->skills)) {
            // We couldn't find the requested skill!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }
        $skill = $this->skills[$id];
        $skill['links']['self'] = $this->links['self'] = sprintf(
            '/api/shadowrun5e/skills/%s',
            $id
        );

        $this->headers['Etag'] = sha1((string)json_encode($skill));
        $data = [
            'links' => $this->links,
            'data' => $skill,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
