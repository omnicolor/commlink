<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5E;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Controller for the various Shadowrun skill requests.
 */
class SkillsController extends Controller
{
    /**
     * Filename for all of the skills.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of skills.
     * @var array<string, mixed>
     */
    protected array $skills;

    /**
     * Constructor.
     * @throws \ErrorException if the path to the data file is wrong
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e') . 'skills.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/skills';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->skills = require $this->filename;
    }

    /**
     * Return all skills.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->skills as $key => $value) {
            $this->skills[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/skills/%s',
                    \urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = \sha1_file($this->filename);
        $data = [
            'links' => $this->links,
            'data' => \array_values($this->skills),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single skill.
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        if (!\array_key_exists($id, $this->skills)) {
            // We couldn't find the requested skill!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }
        $skill = $this->skills[$id];
        $skill['links']['self'] = $this->links['self'] = \sprintf(
            '/api/shadowrun5e/skills/%s',
            $id
        );

        $this->headers['Etag'] = \sha1((string)\json_encode($skill));
        $data = [
            'links' => $this->links,
            'data' => $skill,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
