<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function array_key_exists;
use function array_values;
use function config;
use function date;
use function json_encode;
use function response;
use function route;
use function sha1;
use function sha1_file;
use function stat;
use function strtolower;

/**
 * Controller for resonance echoes.
 */
class ResonanceEchoesController extends Controller
{
    /**
     * Collection of all resonance echoes.
     * @var array<string, array<string, array<string, int|string>|int|string>>
     */
    protected array $echoes;

    protected string $filename;

    public function __construct()
    {
        parent::__construct();
        $this->filename = config('shadowrun5e.data_path')
            . 'resonance-echoes.php';

        $this->echoes = require $this->filename;

        $stat = stat($this->filename);
        assert(false !== $stat); // require() would have failed.
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
    }

    /**
     * @param array<string, array<string, int|string>|int|string> $echo
     * @return array<string, array<string, int|string>|int|string>
     */
    protected function cleanup(array $echo): array
    {
        unset($echo['chummer-id']);
        $echo['links'] = [
            'self' => route('shadowrun5e.resonance-echoes.show', $echo['id']),
        ];
        return $echo;
    }

    public function index(): Response
    {
        foreach ($this->echoes as $key => $echo) {
            $this->echoes[$key] = $this->cleanup($echo);
        }

        $this->headers['Etag'] = sha1_file($this->filename);
        $this->links['self'] = route('shadowrun5e.resonance-echoes.index');
        $data = [
            'links' => $this->links,
            'data' => array_values($this->echoes),
        ];

        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }

    public function show(string $id): Response
    {
        $id = strtolower($id);
        abort_if(
            !array_key_exists($id, $this->echoes),
            Response::HTTP_NOT_FOUND,
            $id . ' not found',
        );
        $echo = $this->cleanup($this->echoes[$id]);

        $this->headers['Etag'] = sha1((string)json_encode($echo));
        $this->links['collection'] = route('shadowrun5e.resonance-echoes.index');
        $this->links['self'] = route('shadowrun5e.resonance-echoes.show', $id);
        $data = [
            'links' => $this->links,
            'data' => $echo,
        ];
        return response($data, Response::HTTP_OK)->withHeaders($this->headers);
    }
}
