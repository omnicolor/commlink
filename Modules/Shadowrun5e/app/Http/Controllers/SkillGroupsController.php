<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Controller for Shadowrun 5E skill groups.
 * @psalm-suppress UnusedClass
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

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e') . 'skills.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/skill-groups';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);

        /** @psalm-suppress UnresolvableInclude */
        $skills = require $this->filename;
        foreach ($skills as $key => $value) {
            if (!\array_key_exists('group', $value)) {
                // Some skills are not in any groups.
                continue;
            }
            $value['links']['self'] = \sprintf(
                '/api/shadowrun5e/skills/%s',
                \urlencode((string)$key)
            );
            // @phpstan-ignore-next-line
            $this->groups[$value['group']]['skills'][] = $value;
            // @phpstan-ignore-next-line
            $this->groups[$value['group']]['id'] = $value['group'];
            // @phpstan-ignore-next-line
            $this->groups[$value['group']]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/skill-groups/%s',
                    $value['group']
                ),
            ];
        }
    }

    /**
     * Return all skill groups.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function index(): Response
    {
        $this->headers['Etag'] = \sha1_file($this->filename);
        $data = [
            'links' => $this->links,
            'data' => \array_values($this->groups),
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single skill group.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function show(string $identifier): Response
    {
        $identifier = \strtolower($identifier);
        if (!\array_key_exists($identifier, $this->groups)) {
            // We couldn't find the requested skill group!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $identifier . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $group = $this->groups[$identifier];
        $this->headers['Etag'] = \sha1((string)\json_encode($group));
        $this->links['self'] = \sprintf(
            '/api/shadowrun5e/skill-groups/%s',
            $identifier
        );
        $data = [
            'links' => $this->links,
            'data' => $group,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
