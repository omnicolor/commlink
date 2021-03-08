<?php

declare(strict_types=1);

namespace App\Http\Controllers\Expanse;

use Illuminate\Http\Response;

/**
 * Controller for Expanse social classes.
 */
class SocialClassesController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all social classes.
     * @var array<string, mixed>
     */
    protected array $classes;

    /**
     * Constructor.
     * @throws \ErrorException if the path to the data file is wrong
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.expanse')
            . 'social-classes.php';
        $this->links['system'] = '/api/expanse';
        $this->links['collection'] = '/api/expanse/social-classes';
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        $this->classes = require $this->filename;
    }

    /**
     * Get the entire collection of Expanse social classes.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->classes as $key => $unused) {
            $this->classes[$key]['links'] = [
                'self' => sprintf('/api/expanse/social-classes/%s', $key),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->classes),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Get a single Expanse social class.
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id): Response
    {
        $id = strtolower($id);
        if (!array_key_exists($id, $this->classes)) {
            // We couldn't find it!
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => sprintf('%s not found', $id),
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $class = $this->classes[$id];
        $class['links']['self'] = $this->links['self'] =
            sprintf('/api/expanse/social-classes/%s', $id);

        $this->headers['Etag'] = sha1((string)json_encode($class));

        $data = [
            'links' => $this->links,
            'data' => $class,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
