<?php

declare(strict_types=1);

namespace App\Http\Controllers\Expanse;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function date;
use function json_encode;
use function sha1;
use function sha1_file;
use function stat;
use function strtolower;

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
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        $this->conditions = require $this->filename;
    }

    /**
     * Get the entire collection of Expanse conditions.
     */
    public function index(): Response
    {
        foreach (array_keys($this->conditions) as $key) {
            $this->conditions[$key]['links'] = [
                'self' => route('expanse.conditions.show', ['condition' => $key]),
            ];
            $this->conditions[$key]['id'] = $key;
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $this->links['self'] = route('expanse.conditions.index');

        $data = [
            'links' => $this->links,
            'data' => $this->conditions,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single Expanse condition.
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->conditions),
            Response::HTTP_NOT_FOUND,
            sprintf('%s not found', $id),
        );

        $condition = $this->conditions[$id];
        $condition['links']['self'] = $this->links['self'] =
            route('expanse.conditions.show', ['condition' => $id]);
        $condition['id'] = $id;

        $this->headers['Etag'] = sha1((string)json_encode($condition));
        $this->links['collection'] = route('expanse.conditions.index');

        $data = [
            'links' => $this->links,
            'data' => $condition,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
