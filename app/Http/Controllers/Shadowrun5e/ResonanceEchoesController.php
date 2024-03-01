<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5e;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

use function array_key_exists;
use function json_encode;
use function sha1;
use function sha1_file;

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
        $this->filename = config('app.data_path.shadowrun5e')
            . 'resonance-echoes.php';

        $stat = stat($this->filename);
        // @phpstan-ignore-next-line
        $this->headers['Last-Modified'] = date('r', $stat['mtime']);
        $this->echoes = require $this->filename;
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
