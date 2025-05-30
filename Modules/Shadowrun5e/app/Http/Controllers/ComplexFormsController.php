<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function abort_if;
use function array_key_exists;
use function array_keys;
use function array_values;
use function assert;
use function config;
use function date;
use function json_encode;
use function response;
use function sha1;
use function sha1_file;
use function sprintf;
use function stat;
use function strtolower;

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
        $this->filename = config('shadowrun5e.data_path')
            . 'complex-forms.php';
        $this->links['collection'] = route('shadowrun5e.complex-forms.index');

        $this->forms = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
    }

    /**
     * Return the entire collection of complex forms.
     */
    public function index(): Response
    {
        foreach (array_keys($this->forms) as $key) {
            $this->forms[$key]['links'] = [
                'self' => route('shadowrun5e.complex-forms.show', $key),
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
     */
    public function show(string $identifier): Response
    {
        $identifier = strtolower($identifier);
        abort_if(
            !array_key_exists($identifier, $this->forms),
            Response::HTTP_NOT_FOUND,
            sprintf('%s not found', $identifier),
        );

        $form = $this->forms[$identifier];
        $form['links']['self'] = $this->links['self'] =
            route('shadowrun5e.complex-forms.show', $identifier);

        $this->headers['Etag'] = sha1((string)json_encode($form));

        $data = [
            'links' => $this->links,
            'data' => $form,
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
