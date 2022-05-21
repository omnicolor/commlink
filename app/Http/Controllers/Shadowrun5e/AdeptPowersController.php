<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5e;

use Illuminate\Http\Response;

/**
 * Controller for adept powers.
 */
class AdeptPowersController extends \App\Http\Controllers\Controller
{
    /**
     * Filename for all of the data.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of adept powers.
     * @var array<string, mixed>
     */
    protected array $powers;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'adept-powers.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/adept-powers';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->powers = require $this->filename;
    }

    /**
     * Get the entire collection of fifth edition adept powers.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->powers as $key => $value) {
            $this->powers[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/adept-powers/%s',
                    \urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->powers),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single fifth edition adept power.
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        $id = \strtolower($id);
        if (!\array_key_exists($id, $this->powers)) {
            // We couldn't find it!
            $errors = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => $id . ' not found',
                'title' => 'Not Found',
            ];
            return $this->error($errors);
        }
        $power = $this->powers[$id];

        $power['links']['self'] = $this->links['self'] =
            \sprintf('/api/shadowrun5e/adept-powers/%s', $id);

        $this->headers['Etag'] = \sha1((string)\json_encode($power));

        $data = [
            'links' => $this->links,
            'data' => $power,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
