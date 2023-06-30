<?php

declare(strict_types=1);

namespace App\Http\Controllers\Expanse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Controller for Expanse conditions.
 */
class ConditionsController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all conditions.
     * @var array<string, mixed>
     */
    protected array $conditions;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.expanse') . 'conditions.php';
        $this->links['system'] = '/api/expanse';
        $this->links['collection'] = '/api/expanse/conditions';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->conditions = require $this->filename;
    }

    /**
     * Get the entire collection of Expanse conditions.
     */
    public function index(): Response
    {
        foreach (array_keys($this->conditions) as $key) {
            $this->conditions[$key]['links'] = [
                'self' => \sprintf('/api/expanse/conditions/%s', $key),
            ];
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->conditions),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single Expanse condition.
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->conditions)) {
            // We couldn't find it!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => \sprintf('%s not found', $id),
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $condition = $this->conditions[$id];
        $condition['links']['self'] = $this->links['self'] =
            \sprintf('/api/expanse/conditions/%s', $id);

        $this->headers['Etag'] = \sha1((string)\json_encode($condition));

        $data = [
            'links' => $this->links,
            'data' => $condition,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
