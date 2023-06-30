<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5e;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * Controller for complex forms.
 */
class ComplexFormsController extends Controller
{
    /**
     * Path to the data file.
     */
    protected string $filename;

    /**
     * Collection of all complex forms.
     * @var array<string, mixed>
     */
    protected array $forms;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('app.data_path.shadowrun5e')
            . 'complex-forms.php';
        $this->links['system'] = '/api/shadowrun5e';
        $this->links['collection'] = '/api/shadowrun5e/complex-forms';
        $stat = \stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = \date('r', $stat['mtime']);
        $this->forms = require $this->filename;
    }

    /**
     * Return the entire collection of complex forms.
     */
    public function index(): Response
    {
        foreach (array_keys($this->forms) as $key) {
            $this->forms[$key]['links'] = [
                'self' => \sprintf(
                    '/api/shadowrun5e/complex-forms/%s',
                    \urlencode($key)
                ),
            ];
        }

        $this->headers['Etag'] = \sha1_file($this->filename);

        $data = [
            'links' => $this->links,
            'data' => \array_values($this->forms),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    /**
     * Return a single complex form.
     */
    public function show(string $identifier): Response
    {
        $identifier = \strtolower($identifier);
        if (!\array_key_exists($identifier, $this->forms)) {
            $error = [
                'status' => Response::HTTP_NOT_FOUND,
                'detail' => \sprintf('%s not found', $identifier),
                'title' => 'Not Found',
            ];
            return $this->error($error);
        }

        $form = $this->forms[$identifier];
        $form['links']['self'] = $this->links['self'] =
            \sprintf('/api/shadowrun5e/complex-forms/%s', $identifier);

        $this->headers['Etag'] = \sha1((string)\json_encode($form));

        $data = [
            'links' => $this->links,
            'data' => $form,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
