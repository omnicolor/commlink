<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Character;
use App\Models\User;
use ErrorException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToCreateDirectory;
use ParseError;
use Throwable;

use function ucwords;

/**
 * @psalm-suppress UnusedClass
 */
class VarzController extends Controller
{
    /**
     * Collection mapping the different supported systems to the example data
     * files.
     */
    protected const SYSTEM_MAP = [
        'alien' => 'Modules/Alien/data/',
        'avatar' => 'Modules/Avatar/data/',
        'blistercritters' => 'Modules/Blistercritters/data/',
        'capers' => 'Modules/Capers/data/',
        'cyberpunkred' => 'Modules/Cyberpunkred/data/',
        'dnd5e' => 'Modules/Dnd5e/data/',
        'expanse' => 'Modules/Expanse/data/',
        'shadowrun5e' => 'Modules/Shadowrun5e/data/',
        'shadowrun6e' => 'Modules/Shadowrun6e/data/',
        'startrekadventures' => 'Modules/Startrekadventures/data/',
        'stillfleet' => 'Modules/Stillfleet/data/',
        'subversion' => 'Modules/Subversion/data/',
        'transformers' => 'Modules/Transformers/data/',
    ];

    public function index(): Response
    {
        $systems = config('app.systems');

        $data = [
            'campaigns-total' => Campaign::count(),
            'channels' => [
                // @phpstan-ignore staticMethod.dynamicCall
                'discord' => Channel::discord()->count(),
                // @phpstan-ignore staticMethod.dynamicCall
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
        return new Response($data);
    }

    /**
     * Return metrics about individual systems.
     * @return array<string, int>
     * @psalm-suppress InvalidStringClass
     */
    protected function getSystemMetrics(string $system): array
    {
        $characterClass = sprintf(
            '\\Modules\\%s\\Models\\Character',
            str_replace(' ', '', ucwords(str_replace('-', ' ', $system)))
        );
        try {
            $metrics = [
                // @phpstan-ignore staticMethod.dynamicCall
                'campaigns' => Campaign::where('system', $system)->count(),
                'player-characters' => $characterClass::count(),
            ];
        } catch (Throwable) { // @codeCoverageIgnoreStart
            $metrics = [
                'campaigns' => 0,
                'player-characters' => 0,
            ];
        } // @codeCoverageIgnoreEnd

        $path = config($system . '.data_path');
        if (null === $path) {
            return $metrics; // @codeCoverageIgnore
        }
        try {
            $dataFiles = Storage::build([
                'driver' => 'local',
                'root' => $path,
            ])->files();
        } catch (UnableToCreateDirectory | ErrorException) { // @codeCoverageIgnore
            return $metrics; // @codeCoverageIgnore
        }

        if (!array_key_exists($system, self::SYSTEM_MAP)) {
            // @codeCoverageIgnoreStart
            Log::warning(
                'Varz: Missing system example directory',
                ['system' => $system],
            );
            $exampleFiles = [];
            // @codeCoverageIgnoreEnd
        } else {
            $exampleFiles = Storage::build([
                'driver' => 'local',
                'root' => base_path(self::SYSTEM_MAP[$system]),
            ])->files();
        }
        foreach ($dataFiles as $file) {
            if (!in_array($file, $exampleFiles, true)) {
                continue; // @codeCoverageIgnore
            }
            try {
                /** @psalm-suppress UnresolvableInclude */
                $data = require $path . $file;
            } catch (ParseError) { // @codeCoverageIgnore
                continue; // @codeCoverageIgnore
            }
            if (!is_array($data)) {
                continue; // @codeCoverageIgnore
            }
            /** @psalm-suppress PossiblyInvalidCast */
            $file = (string)str_replace('.php', '', $file);
            $metrics[$file] = count($data);
        }
        return $metrics;
    }
}
