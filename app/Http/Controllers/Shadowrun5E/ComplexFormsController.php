<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5E;

use Illuminate\Http\Response;

/**
 * Controller for complex forms.
 */
class ComplexFormsController extends \App\Http\Controllers\Controller
{
    /**
     * Path to the data file.
     * @var string
     */
    protected string $filename;

    /**
     * Collection of all complex forms.
     * @var array<string, mixed>
     */
    protected array $forms;

    /**
     * Constructor.
     * @throws \ErrorException if the path to the data file is wrong
     */
    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'complex-forms.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/complex-forms';
        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        $this->forms = require $this->filename;
    }

    /**
     * Return the entire collection of complex forms.
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        foreach ($this->forms as $key => $unused) {
            $this->forms[$key]['links'] = [
                'self' => sprintf('/api/shadowrun5e/complex-forms/%s', $key),
            ];
        }

        $this->headers['Etag'] = sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => array_values($this->forms),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single complex form.
     * @param string $identifier
     * @return \Illuminate\Http\Response
     */
    public function show(string $identifier): Response
    {
        $identifier = strtolower($identifier);
        if (!key_exists($identifier, $this->forms)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => sprintf('%s not found', $identifier),
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $form = $this->forms[$identifier];
        $form['links']['self'] = $this->links['self'] =
            sprintf('/api/shadowrun5e/complex-forms/%s', $identifier);

        $this->headers['Etag'] = sha1((string)json_encode($form));

        $data = [
            'links' => $this->links,
            'data' => $form,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
