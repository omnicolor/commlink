<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\UnableToCreateDirectory;
use Nwidart\Modules\Facades\Module;
use ParseError;

use function count;
use function in_array;
use function is_array;
use function sprintf;

/**
 * Test all data files for correctness.
 * @codeCoverageIgnore
 */
class ValidateDataFiles extends Command
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
        'legendofthefiverings4e' => 'Modules/Legendofthefiverings4e/data/',
        'root' => 'Modules/Root/data/',
        'shadowrunanarchy' => 'Modules\Shadowrunanarchy/data/',
        'shadowrun5e' => 'Modules/Shadowrun5e/data/',
        'shadowrun6e' => 'Modules/Shadowrun6e/data/',
        'startrekadventures' => 'Modules/Startrekadventures/data/',
        'stillfleet' => 'Modules/Stillfleet/data/',
        'subversion' => 'Modules/Subversion/data/',
        'transformers' => 'Modules/Transformers/data/',
    ];

    /** @var string */
    protected $signature = 'commlink:validate-data-files';

    /** @var string */
    protected $description = 'Check data files installed for all enabled systems';

    /**
     * Paths configured to where system data is kept.
     * @var array<string, string>
     */
    protected array $paths;

    protected int $return = self::SUCCESS;

    public function handle(): int
    {
        $systems = config('commlink.systems');
        $this->line('Enabled systems to be validated:');
        foreach ($systems as $system) {
            $this->line('  * ' . $system);
        }

        $this->paths = [];
        foreach (Module::all() as $system) {
            $this->paths[(string)$system->getLowerName()] =
                (string)config($system->getLowerName() . '.data_path');
        }
        foreach ($systems as $system => $full) {
            $this->line($full);

            if (!isset($this->paths[$system])) {
                $this->error('  * No data_path config set');
                $this->return = self::FAILURE;
                continue;
            }

            if (
                $this->paths[$system] === self::SYSTEM_MAP[$system]
                && !file_exists(base_path($this->paths[$system]))
            ) {
                $this->error(
                    '  * Invalid data directory: ' . $this->paths[$system]
                );
                $this->return = self::FAILURE;
                continue;
            }

            if (!isset(self::SYSTEM_MAP[$system])) {
                $this->warn('  * Not included in ValidateDataFiles system map');
                continue;
            }

            if ($this->paths[$system] === self::SYSTEM_MAP[$system]) {
                $this->warn('  * Using default data files');
            } else {
                $this->line('  * Using data files in ' . $this->paths[$system]);
            }
            if ($this->checkForEmptyDirectory($system)) {
                $this->warn('  * No data files found');
                continue;
            }
            if ($this->paths[$system] !== self::SYSTEM_MAP[$system]) {
                $this->checkForMissingFiles($system);
                $this->checkForExtraFiles($system);
            }
            $this->validateDataFiles($system);
        }
        return $this->return;
    }

    protected function validateDataFiles(string $system): void
    {
        $dataFiles = Storage::build([
            'driver' => 'local',
            'root' => $this->paths[$system],
        ])->files();
        $exampleFiles = Storage::build([
            'driver' => 'local',
            'root' => base_path(self::SYSTEM_MAP[$system]),
        ])->files();
        foreach ($dataFiles as $file) {
            if (!in_array($file, $exampleFiles, true)) {
                continue;
            }
            try {
                $data = require $this->paths[$system] . $file;
            } catch (ParseError $ex) {
                $this->return = Command::FAILURE;
                $this->error(sprintf(
                    '  * %s is not valid on line %d: %s',
                    $file,
                    $ex->getLine(),
                    $ex->getMessage(),
                ));
                continue;
            }
            if (!is_array($data)) {
                $this->return = Command::FAILURE;
                $this->error(sprintf(
                    '  * %s does not return a PHP array of data',
                    $file
                ));
                continue;
            }
            $this->info(sprintf(
                '  * %s contains %d data %s',
                $file,
                count($data),
                Str::plural('element', count($data))
            ));
        }
    }

    protected function checkForExtraFiles(string $system): void
    {
        $exampleFiles = Storage::build([
            'driver' => 'local',
            'root' => base_path(self::SYSTEM_MAP[$system]),
        ])->files();
        $dataFiles = Storage::build([
            'driver' => 'local',
            'root' => $this->paths[$system],
        ])->files();
        $unexpectedFiles = array_diff($dataFiles, $exampleFiles);
        foreach ($unexpectedFiles as $file) {
            $this->warn('  * Unexpected data file: ' . $file);
        }
    }

    protected function checkForMissingFiles(string $system): void
    {
        $exampleFiles = Storage::build([
            'driver' => 'local',
            'root' => base_path(self::SYSTEM_MAP[$system]),
        ])->files();
        $dataFiles = Storage::build([
            'driver' => 'local',
            'root' => $this->paths[$system],
        ])->files();
        $missingFiles = array_diff($exampleFiles, $dataFiles);
        foreach ($missingFiles as $file) {
            $this->return = Command::FAILURE;
            $this->error('  * Missing data file: ' . $file);
        }
    }

    protected function checkForEmptyDirectory(string $system): bool
    {
        try {
            $dataFiles = Storage::build([
                'driver' => 'local',
                'root' => $this->paths[$system],
            ])->files();
        } catch (UnableToCreateDirectory) {
            return true;
        }
        return 0 === count($dataFiles);
    }
}
