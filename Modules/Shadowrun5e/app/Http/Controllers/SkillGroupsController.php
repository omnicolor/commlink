<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Response;

use function abort_if;
use function array_key_exists;
use function array_values;
use function assert;
use function config;
use function json_encode;
use function response;
use function route;
use function sha1;
use function sha1_file;
use function stat;
use function strtolower;

/**
 * Controller for Shadowrun 5E skill groups.
 */
class SkillGroupsController extends Controller
{
    /**
     * Filename for the data file.
     */
    protected string $filename;

    /**
     * Collection of skill groups.
     * @var array<string, mixed>
     */
    protected array $groups = [];

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path') . 'skills.php';
        $this->links['collection'] = route('shadowrun5e.skill-groups.index');

        $skills = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = Carbon::createFromTimestamp($stat['mtime'])->format('r');

        foreach ($skills as $skill_id => $skill) {
            if (!array_key_exists('group', $skill)) {
                // Some skills are not in any groups.
                continue;
            }
            /** @var string */
            $group = $skill['group'];

            $skill['links']['self'] = route('shadowrun5e.skills.show', $skill_id);
            $this->groups[$group]['skills'][] = $skill;
            $this->groups[$group]['id'] = $skill['group'];
            $this->groups[$group]['links'] = [
                'self' => route('shadowrun5e.skill-groups.show', $group),
            ];
        }
    }

    /**
     * Return all skill groups.
     */
    public function index(): Response
    {
        $this->headers['Etag'] = sha1_file($this->filename);
        $data = [
            'links' => $this->links,
            'data' => array_values($this->groups),
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single skill group.
     */
    public function show(string $identifier): Response
    {
        $identifier = strtolower($identifier);
        abort_if(
            !array_key_exists($identifier, $this->groups),
            Response::HTTP_NOT_FOUND,
            $identifier . ' not found',
        );

        $group = $this->groups[$identifier];
        $this->headers['Etag'] = sha1((string)json_encode($group));
        $this->links['self'] = route('shadowrun5e.skill-groups.show', $identifier);
        $data = [
            'links' => $this->links,
            'data' => $group,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
