<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Character;
use App\Models\User;
use ErrorException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToCreateDirectory;
use ParseError;

class VarzController extends Controller
{
    /**
     * Collection mapping the different supported systems to the example data
     * files.
     */
    protected const SYSTEM_MAP = [
        'avatar' => 'data/Avatar/',
        'capers' => 'data/Capers/',
        'cyberpunkred' => 'data/Cyberpunkred/',
        'dnd5e' => 'data/Dnd5e/',
        'expanse' => 'data/Expanse/',
        'shadowrun5e' => 'data/Shadowrun5e/',
        'star-trek-adventures' => 'data/StarTrekAdventures/',
    ];

    public function index(): Response
    {
        $systems = config('app.systems');
        $data = [
            'campaigns-total' => Campaign::count(),
            'channels' => [
                // @phpstan-ignore-next-line
                'discord' => Channel::discord()->count(),
                // @phpstan-ignore-next-line
                'slack' => Channel::slack()->count(),
            ],
            'characters-total' => Character::count(),
            'systems' => [],
            'users' => User::count(),
        ];
        foreach ($systems as $code => $name) {
            $data['systems'][$code] = [
                'name' => $name,
                'data' => $this->getSystemMetrics($code),
            ];
        }
        return response($data);
    }

    /**
     * Return metrics about individual systems.
     * @param string $system
     * @return array<string, int>
     */
    protected function getSystemMetrics(string $system): array
    {
        $characterClass = sprintf(
            '\\App\\Models\\%s\\Character',
            str_replace(' ', '', \ucwords(str_replace('-', ' ', $system)))
        );
        $metrics = [
            // @phpstan-ignore-next-line
            'campaigns' => Campaign::where('system', $system)->count(),
            'player-characters' => $characterClass::count(),
        ];

        $paths = config('app.data_path');
        try {
            $dataFiles = Storage::build([
                'driver' => 'local',
                'root' => $paths[$system],
            ])->files();
        } catch (UnableToCreateDirectory | ErrorException) {
            return $metrics;
        }

        $exampleFiles = Storage::build([
            'driver' => 'local',
            'root' => base_path(self::SYSTEM_MAP[$system]),
        ])->files();
        foreach ($dataFiles as $file) {
            if (!in_array($file, $exampleFiles, true)) {
                continue; // @codeCoverageIgnore
            }
            try {
                $data = require $paths[$system] . $file;
            } catch (ParseError $ex) { // @codeCoverageIgnore
                continue; // @codeCoverageIgnore
            }
            if (!is_array($data)) {
                continue; // @codeCoverageIgnore
            }
            $file = (string)str_replace('.php', '', $file);
            $metrics[$file] = count($data);
        }
        return $metrics;
    }
}
