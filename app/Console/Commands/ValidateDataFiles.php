<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ParseError;

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
        'avatar' => 'data/Avatar/',
        'capers' => 'data/Capers/',
        'cyberpunkred' => 'data/Cyberpunkred/',
        'dnd5e' => 'data/Dnd5e/',
        'expanse' => 'data/Expanse/',
        'shadowrun5e' => 'data/Shadowrun5e/',
        'shadowrun6e' => 'data/Shadowrun6e/',
        'star-trek-adventures' => 'data/StarTrekAdventures/',
    ];

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'commlink:validate-data-files';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Check data files installed for all enabled systems';

    /**
     * @var array<string, string> Paths configured to where system data is kept.
     */
    protected array $paths;

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int
    {
        $systems = config('app.systems');
        $this->line('Enabled systems to be validated:');
        foreach ($systems as $system) {
            $this->line('  * ' . $system);
        }

        $this->paths = config('app.data_path');
        foreach ($systems as $system => $full) {
            $this->line($full);

            if (!isset($this->paths[$system])) {
                $this->error('  * No data_path config set');
                continue;
            }

            if (!file_exists($this->paths[$system])) {
                $this->error(
                    '  * Invalid data directory: ' . $this->paths['system']
                );
                continue;
            }

            if ($this->paths[$system] === self::SYSTEM_MAP[$system]) {
                $this->warn('  * Using default data files');
            } else {
                $this->line('  * Using data files in ' . $this->paths[$system]);
            }
            if ($this->checkForEmptyDirectory($system)) {
                $this->error('  * No data files found');
                continue;
            }
            if ($this->paths[$system] !== self::SYSTEM_MAP[$system]) {
                $this->checkForMissingFiles($system);
                $this->checkForExtraFiles($system);
            }
            $this->validateDataFiles($system);
        }
        return 0;
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
                $this->error(sprintf(
                    '  * %s is not valid on line %d: %s',
                    $file,
                    $ex->getLine(),
                    $ex->getMessage(),
                ));
                continue;
            }
            if (!is_array($data)) {
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
            $this->error('  * Missing data file: ' . $file);
        }
    }

    protected function checkForEmptyDirectory(string $system): bool
    {
        $dataFiles = Storage::build([
            'driver' => 'local',
            'root' => $this->paths[$system],
        ])->files();
        return 0 === count($dataFiles);
    }
}
