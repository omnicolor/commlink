<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Shadowrun5e\ForceTrait;
use GitElephant\Repository;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use SimpleXMLElement;

/**
 * @codeCoverageIgnore
 * @psalm-suppress InvalidArgument
 * @psalm-suppress UnusedClass
 */
class ImportChummerData extends Command implements Isolatable
{
    use ForceTrait;

    /**
     * List of valid types that can be imported.
     */
    protected const DATA_TYPES = [
        'armor',
        'augmentations',
        'complex-forms',
        'critter-powers',
        'weapons',
    ];

    /**
     * Mapping of Chummer source codes to Commlink ruleset names.
     */
    protected const SOURCE_MAP = [
        '2050' => 'core',
        'AET' => 'aetherology',
        'AP' => 'assassins-primer',
        'BB' => 'bullets-and-bandages',
        'BLB' => 'bloody-business',
        'BOTL' => 'book-of-the-lost',
        'BTB' => 'better-than-bad',
        'CA' => 'cutting-aces',
        'CF' => 'chrome-flesh',
        'DT' => 'data-trails',
        'DTR' => 'dark-terrors',
        'DTD' => null, // Data Trails (Dissonant Echoes)
        'DPVG' => null, // Datapuls Verschlusssache (German)
        'FA' => 'forbidden-arcana',
        'GE' => null, // Grimmes Erwachen (German)
        'GH3' => 'gun-heaven-3',
        'HAMG' => null, // Hamburg (German)
        'HKS' => null, // Hong Kong Sourcebook
        'HS' => 'howling-shadows',
        'HT' => 'hard-targets',
        'KC' => 'kill-code',
        'KK' => null, // Krime Katalog
        'LCD' => 'lockdown',
        'NF' => 'no-future',
        'NP' => null, // Nothing Personal
        'QSR' => null, // Shadowrun Quick-Start Rules
        'R5' => 'rigger-5',
        'RC' => null, // Unknown code for critter powers (and maybe others)
        'RF' => 'run-faster',
        'RG' => 'run-and-gun',
        'SAG' => null, // State of the Art ADL (German)
        'SASS' => null, // Sail Away Sweet Sister
        'SGB' => 'shadows-in-focus-butte',
        'SFCC' => 'shadows-in-focus-cheyenne',
        'SFM' => 'shadows-in-focus-san-francisco-metroplex',
        'SFME' => null, // 'shadows-in-focus-metropole',
        'SG' => 'street-grimoire',
        'SGE' => null, // Street Grimoire errata
        'SHB' => null, // Schattenhandbuch (German)
        'SHB2' => null, // Schattenhandbuch 2 (German)
        'SHB3' => null, // Schattenhandbuch 3 (German)
        'SL' => 'street-lethal',
        'SOTG' => null, // Datapuls SOTA 2080 (German)
        'SR4' => null, // Unknown code for critter powers (and maybe others)
        'SR5' => 'core',
        'SRM0803' => null, // Shadowrun Missions 0803: 10 Block Tango
        'SRM0804' => null, // Shadowrun Missions 0804: Dirty Laundry
        'SPS' => 'splintered-state',
        'SS' => 'stolen-souls',
        'SSP' => 'shadow-spells',
        'SW' => 'sprawl-wilds',
        'TCT' => 'complete-trog',
        'TSG' => null, // The Seattle Gambit
        'TVG' => 'vladivostok-guantlet',
        'UN' => null, // Unknown code for critter powers (and maybe others)
        'WAR' => null, // Unknown code for critter powers (and maybe others)
    ];

    /**
     * Path to the Chummer git repository.
     */
    protected string $chummerRepository;

    /**
     * The console command description.
     * @var ?string
     */
    protected $description = 'Import data from Chummer\'s GitHub repository';

    /**
     * Path to write data to.
     */
    protected string $outputDir = 'storage/app/shadowrun5e-data';

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'commlink:import-chummer-data
        {--type=* : Type of data to import}
        {--skip-pull : Don\'t update Chummer\'s git repository}
        {--chummer-path= : Set the path Chummer 5\'s local git repository}
        {--output-dir=storage/app/shadowrun5e-data : Set the output directory}
        {--list-types : List the types of data you can import, then exit}';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (true === $this->option('list-types')) {
            $this->listTypes();
            return 0;
        }

        $this->info('Creating Commlink data files from Chummer 5 data');

        // Don't output warnings loading bad XML files, we'll handle it.
        libxml_use_internal_errors(true);

        // Only allow one run at a time.
        $this->input->setOption('isolated', true);

        $this->validateOptions();

        $this->updateChummerRepository();

        $types = $this->option('type');
        if (null === $types) {
            $types = self::DATA_TYPES;
        }
        // @phpstan-ignore-next-line
        foreach ($types as $type) {
            $function = 'process' . ucfirst(str_replace('-', '', $type));
            // @phpstan-ignore-next-line
            $this->$function();
        }

        return 0;
    }

    protected function listTypes(): void
    {
        $this->info('You can request one or more of the following data types be processed:');
        foreach (self::DATA_TYPES as $type) {
            $this->line(' * ' . $type);
        }
    }

    /**
     * Validate the options from the user, throwing exceptions if needed.
     */
    protected function validateOptions(): void
    {
        $diff = array_diff((array)$this->option('type'), self::DATA_TYPES);

        if (0 !== count($diff)) {
            throw new RuntimeException(
                'Invalid data ' . Str::plural('type', count($diff)) . ': '
                . implode(', ', $diff)
            );
        }

        if (null !== $this->option('output-dir')) {
            // @phpstan-ignore-next-line
            $this->outputDir = (string)$this->option('output-dir');
        }

        if (file_exists($this->outputDir) && !is_dir($this->outputDir)) {
            throw new RuntimeException(
                'Output directory (' . $this->outputDir . ') is not a directory'
            );
        }

        if (!file_exists($this->outputDir)) {
            mkdir($this->outputDir);
            if (!file_exists($this->outputDir)) {
                throw new RuntimeException(
                    'Unable to create output directory: ' . $this->outputDir
                );
            }
        }
    }

    /**
     * Sets the Chummer repository to use and updates it.
     *
     * If the user specifies a chummer-path, makes sure it's valid. If they
     * don't specify, we'll clone it if it doesn't already exist. For
     * repositories that already exist, we'll do a `git pull` to make sure it's
     * up to date (unless the user specifies --skip-pull).
     */
    protected function updateChummerRepository(): void
    {
        $this->chummerRepository = storage_path('app/chummer5a');
        if (null !== $this->option('chummer-path')) {
            // @phpstan-ignore-next-line
            $this->chummerRepository = (string)$this->option('chummer-path');
            if (!file_exists($this->chummerRepository)) {
                throw new RuntimeException(
                    'Path given by --chummer-path does not exist'
                );
            }
            if (!is_dir($this->chummerRepository)) {
                throw new RuntimeException(
                    'Path given by --chummer-path is not a directory'
                );
            }
            if (
                !file_exists($this->chummerRepository . '/.git')
                && !is_dir($this->chummerRepository . '/.git')
            ) {
                throw new RuntimeException(
                    'Path given by --chummer-path is not a git repository'
                );
            }
        }

        if (
            !file_exists($this->chummerRepository)
            && true === $this->option('skip-pull')
        ) {
            throw new RuntimeException(
                'Chummer 5 repository does not exist, but --skip-pull option used'
            );
        }

        if (!file_exists($this->chummerRepository)) {
            $this->line(\sprintf(
                'Cloning Chummer 5 repository: %s',
                $this->chummerRepository
            ));
            $repo = new Repository($this->chummerRepository);
            $repo->cloneFrom('https://github.com/chummer5a/chummer5a.git');
            return;
        }

        if (true === $this->option('skip-pull')) {
            $this->line('Skipping git update on Chummer repository');
            return;
        }

        $this->line(\sprintf(
            'Updating Chummer 5 repository: %s',
            $this->chummerRepository
        ));
        $repo = new Repository($this->chummerRepository);
        $repo->pull();
    }

    /**
     * Try to load the requested file as XML.
     */
    protected function loadXml(string $file): SimpleXmlElement
    {
        $data = simplexml_load_file($file);
        if (!file_exists($file)) {
            throw new RuntimeException(
                'Chummer 5 data file not found: ' . $file
            );
        }

        if (false === $data) {
            throw new RuntimeException(
                'Chummer 5 data file does not appear to be valid XML: ' . $file
            );
        }

        return $data;
    }

    /**
     * Process Chummer's armor file to a Commlink PHP file.
     */
    protected function processArmor(): void
    {
        $data = $this->loadXml(
            $this->chummerRepository . '/Chummer/data/armor.xml'
        );

        /** @var array<string, array<string, array<string, int>|int|string>> */
        $armors = [];
        $bar = $this->output->createProgressBar(count($data->armors->armor));
        $bar->setFormat('  Armor          %current%/%max% [%bar%] %percent%');
        $bar->start();

        foreach ($data->armors->armor as $armor) {
            if (null === self::SOURCE_MAP[(string)$armor->source]) {
                continue;
            }

            $armorItem = [
                'availability' => $this->cleanAvailability($armor),
                'capacity' => (int)$armor->armorcapacity,
                'chummer-id' => (string)$armor->id,
                'cost' => (int)$armor->cost,
                'name' => (string)$armor->name,
                'page' => (int)$armor->page,
                'rating' => (int)$armor->armor,
                'ruleset' => self::SOURCE_MAP[(string)$armor->source],
            ];
            if (
                null !== $armor->bonus
                && null !== $armor->bonus->limitmodifier
            ) {
                $effect = \strtolower(
                    (string)$armor->bonus->limitmodifier->limit
                );
                $armorItem['effects'] = [
                    $effect => (int)$armor->bonus->limitmodifier->value,
                ];
            }
            $armors[$this->nameToId((string)$armor->name)] = $armorItem;
            $bar->advance();
        }
        $bar->setFormat('  Armor          %current%/%max% [%bar%] -- ' . count($armors) . ' armor');
        $bar->finish();
        $this->newLine();
        // @psalm-suppress InvalidArgument
        $this->writeFile('armor.php', $armors);
    }

    /**
     * Convert Chummer's bioware and cyberware files to Commlink's
     * augmentations file.
     */
    protected function processAugmentations(): void
    {
        $augmentations = [];

        $data = $this->loadXml(
            $this->chummerRepository . '/Chummer/data/bioware.xml'
        );
        $count = count($data->biowares->bioware);
        $bar = $this->output->createProgressBar($count);
        $bar->setFormat('  Augmentations  %current%/%max% [%bar%] %percent% -- %message%');
        $bar->setMessage('bioware');
        $bar->start();
        foreach ($data->biowares->bioware as $aug) {
            $this->addSingleAugmentation($aug, 'bioware', $augmentations);
            $bar->advance();
        }

        $data = $this->loadXml(
            $this->chummerRepository . '/Chummer/data/cyberware.xml'
        );
        $bar->setMessage('cyberware');
        $bar->setMaxSteps($count + count($data->cyberwares->cyberware));
        foreach ($data->cyberwares->cyberware as $aug) {
            $this->addSingleAugmentation($aug, 'cyberware', $augmentations);
            $bar->advance();
        }

        $bar->setFormat(
            '  Augmentations  %current%/%max% [%bar%] -- '
                . number_format(count($augmentations)) . ' augmentations ('
                . number_format($count) . ' bioware, '
                . number_format(count($augmentations) - $count) . ' cyberware)',
        );
        $bar->finish();
        $this->newLine();
        // @psalm-suppress InvalidArgument
        $this->writeFile('cyberware.php', $augmentations);
    }

    /**
     * @param array<int, array<string, int|string>> $augmentations
     */
    protected function addSingleAugmentation(
        SimpleXmlElement $aug,
        string $type,
        array &$augmentations
    ): void {
        if (null === self::SOURCE_MAP[(string)$aug->source]) {
            return;
        }

        $id = $this->nameToId((string)$aug->name);
        $augmentation = [
            'chummer-id' => (string)$aug->id,
            'name' => (string)$aug->name,
            'page' => (int)$aug->page,
            'ruleset' => self::SOURCE_MAP[(string)$aug->source],
            'type' => $type,
        ];

        if (null !== $aug->capacity) {
            $cap = (string)$aug->capacity;
            if (Str::contains($cap, '[')) {
                $augmentation['capacity-cost']
                    = (int)Str::betweenFirst($cap, '[', ']');
            }
        }

        if (null === $aug->rating) {
            $augmentation['availability'] = $this->cleanAvailability($aug);
            $augmentation['cost'] = (int)$aug->cost;
            $augmentation['essence'] = (float)$aug->ess;
            $augmentations[$id] = $augmentation;
            return;
        }

        for ($rating = 1; $rating <= (int)$aug->rating; $rating++) {
            $augmentation['availability'] = $this->cleanAvailability($aug, $rating);
            $augmentation['cost'] = $this->calculateCost((string)$aug->cost, $rating);
            $augmentation['essence'] = $this->calculateEssence((string)$aug->ess, $rating);
            $augmentations[$id . '-' . $rating] = $augmentation;
        }
    }

    /**
     * Convert Chummer's complex forms file to Commlink's file.
     */
    protected function processComplexforms(): void
    {
        $matrixAttributes = [
            'attack',
            'data processing',
            'firewall',
            'sleaze',
        ];

        $data = $this->loadXml(
            $this->chummerRepository . '/Chummer/data/complexforms.xml'
        );

        $bar = $this->output->createProgressBar(count($data->complexforms->complexform));
        $bar->setFormat(' . Complex forms  %current%/%max%    [%bar%] %percent%');
        $bar->start();
        $forms = [];
        foreach ($data->complexforms->complexform as $rawForm) {
            if (null === self::SOURCE_MAP[(string)$rawForm->source]) {
                continue;
            }

            $name = (string)$rawForm->name;
            $form = [
                'chummer-id' => (string)$rawForm->id,
                'duration' => (string)$rawForm->duration,
                'fade' => (string)$rawForm->fv,
                'name' => $name,
                'page' => (int)$rawForm->page,
                'ruleset' => self::SOURCE_MAP[(string)$rawForm->source],
                'target' => (string)$rawForm->target,
            ];

            if (
                Str::startsWith($name, 'Infusion')
                || Str::startsWith($name, 'Diffusion')
            ) {
                foreach ($matrixAttributes as $attribute) {
                    $newName = Str::replace('[Matrix Attribute]', $attribute, $name);
                    $form['name'] = $newName;
                    $forms[$this->nameToId($newName)] = $form;
                }
                continue;
            }

            if (
                null !== $rawForm->required
                && null !== $rawForm->required->oneof
                && null !== $rawForm->required->oneof->quality
            ) {
                $form['stream'] = Str::after(
                    (string)$rawForm->required->oneof->quality,
                    ': ',
                );
            }

            $forms[$this->nameToId($name)] = $form;
            $bar->advance();
        }
        $bar->setFormat('  Complex forms    %current%/%max% [%bar%] -- ' . count($forms) . ' complex forms');
        $bar->finish();
        $this->newLine();
        // @psalm-suppress InvalidArgument
        $this->writeFile('complex-forms.php', $forms);
    }

    /**
     * Convert Chummer's critter powers to Commlink's format.
     */
    protected function processCritterpowers(): void
    {
        $data = $this->loadXml(
            $this->chummerRepository . '/Chummer/data/critterpowers.xml'
        );

        $bar = $this->output->createProgressBar(count($data->powers->power));
        $bar->setFormat('  Critter powers %current%/%max% [%bar%] %percent%');
        $bar->start();
        $powers = [];
        foreach ($data->powers->power as $rawPower) {
            if (null === self::SOURCE_MAP[(string)$rawPower->source]) {
                continue;
            }

            if (null !== $rawPower->hide) {
                continue;
            }

            $powers[$this->nameToId((string)$rawPower->name)] = [
                'action' => (string)$rawPower->action,
                'chummer-id' => (string)$rawPower->id,
                'duration' => (string)$rawPower->duration,
                'name' => (string)$rawPower->name,
                'page' => (int)$rawPower->page,
                'range' => (string)$rawPower->range,
                'ruleset' => self::SOURCE_MAP[(string)$rawPower->source],
                'type' => (string)$rawPower->type,
            ];
        }
        $bar->setFormat('  Critter powers %current%/%max% [%bar%] -- ' . count($powers) . ' powers');
        $bar->finish();
        $this->newLine();
        // @psalm-suppress InvalidArgument
        $this->writeFile('critter-powers.php', $powers);
    }

    /**
     * Process Chummer's weapons file to a Commlink PHP file.
     */
    protected function processWeapons(): void
    {
        $data = $this->loadXml(
            $this->chummerRepository . '/Chummer/data/weapons.xml'
        );

        $weapons = [];
        $bar = $this->output->createProgressBar(count($data->weapons->weapon));
        $bar->setFormat('  Weapons        %current%/%max% [%bar%] %percent%');
        $bar->start();
        foreach ($data->weapons->weapon as $rawWeapon) {
            if (null === self::SOURCE_MAP[(string)$rawWeapon->source]) {
                continue;
            }

            $id = $this->nameToId((string)$rawWeapon->name);
            $weapon = [
                'accuracy' => (int)$rawWeapon->accuracy,
                'armor-piercing' => (int)$rawWeapon->accuracy,
                'chummer-id' => (string)$rawWeapon->id,
                'class' => (string)$rawWeapon->category,
                'damage' => (string)$rawWeapon->damage,
                'name' => (string)$rawWeapon->name,
                'page' => (int)$rawWeapon->page,
                'ruleset' => self::SOURCE_MAP[(string)$rawWeapon->source],
            ];

            if (null === $rawWeapon->maxrating) {
                $weapon['availability'] = $this->cleanAvailability($rawWeapon);
                $weapons[$id] = $weapon;
                $bar->advance();
                continue;
            }

            // For some reason Chummer's data file lists the maximum rating for
            // two grenades from Kill Code as 100000 but enforces max level 10.
            $maxRating = (int)((int)$rawWeapon->maxrating / 10000);
            for ($rating = 1; $rating <= $maxRating; $rating++) {
                $weapon['availability']
                    = $this->cleanAvailability($rawWeapon, $rating);
                $weapon['cost']
                    = $this->calculateCost((string)$rawWeapon->cost, $rating);
                $weapons[$id . '-' . $rating] = $weapon;
            }
            $bar->advance();
        }
        $bar->setFormat('  Weapons        %current%/%max% [%bar%] -- ' . count($weapons) . ' weapons');
        $bar->finish();
        $this->newLine();
        $this->writeFile('weapons.php', $weapons);
    }

    /**
     * Given a Chummer item, return the availability for Commlink.
     *
     * If the availability is zero, replace with an empty string. If it's
     * a rating-based availability, use the rating to figure out the rating
     * code.
     */
    protected function cleanAvailability(
        SimpleXmlElement $item,
        ?int $rating = null
    ): string {
        $availability = (string)$item->avail;

        if (Str::contains($availability, 'Rating')) {
            $formula = Str::between($availability, '(', ')');
            $formula = Str::replace('Rating', 'R', $formula);
            $formula = Str::replace(' ', '', $formula);
            $formula = Str::replace('{', '', $formula);
            $formula = Str::replace('}', '', $formula);
            return $this->convertFormula($formula, 'R', (int)$rating) .
                Str::after($availability, ')');
        }
        if ('0' === $availability) {
            return '';
        }
        return $availability;
    }

    /**
     * Given a rating-based cost formula, return the cost for a given rating.
     */
    protected function calculateCost(string $cost, int $rating): int
    {
        $formula = Str::replace('Rating', 'R', $cost);
        $formula = Str::replace(' ', '', $formula);
        $formula = Str::replace('{', '', $formula);
        $formula = Str::replace('}', '', $formula);
        return $this->convertFormula($formula, 'R', $rating);
    }

    /**
     * Given a rating-based essence formula, return the essence cost for the
     * standard-grade of the 'ware.
     */
    protected function calculateEssence(string $essence, int $rating): float
    {
        $formula = Str::replace('Rating', 'R', $essence);
        $formula = Str::replace(' ', '', $formula);
        $cost = (float)Str::after($formula, '*') * 100;
        $formula = Str::replace(Str::after($formula, '*'), (string)$cost, $formula);
        $cost = $this->convertFormula($formula, 'R', $rating) / 100;
        return $cost;
    }

    /**
     * Writes the given data to a file as a PHP array.
     * @param array<string, array<string, array<string, int>|int|string>> $data
     */
    protected function writeFile(string $file, array $data): void
    {
        $output = '<?php' . \PHP_EOL
            . \PHP_EOL
            . 'declare(strict_types=1);' . \PHP_EOL
            . \PHP_EOL
            . 'return [' . \PHP_EOL;

        ksort($data);
        foreach ($data as $id => $item) {
            ksort($item);
            $output .= '    \'' . $id . '\' => [' . \PHP_EOL;
            foreach ($item as $key => $value) {
                $output .= $this->writeLine(2, $key, $value);
            }
            $output .= '    ],' . \PHP_EOL;
        }

        $output .= '];' . \PHP_EOL;
        file_put_contents($this->outputDir . '/' . $file, $output);
    }

    /**
     * Format a single $key and $value for data file output, with padding.
     *
     * Adds the correct number of spaces for the current level, then the array
     * key, and the value. If the value is an array, recursively calls this
     * function to format it better.
     * @param array<string, int|string>|float|int|string $value
     */
    protected function writeLine(
        int $level,
        string $key,
        array|float|int|string $value
    ): string {
        $padding = str_repeat(' ', $level * 4);
        $output = $padding . '\'' . $key . '\' => ';
        if (is_array($value)) {
            $output .= '[' . \PHP_EOL;
            foreach ($value as $subKey => $subValue) {
                $output .= $this->writeLine($level + 1, $subKey, $subValue);
            }
            $output .= $padding . ']';
        } elseif (is_numeric($value) && 'availability' !== $key) {
            $output .= $value;
        } else {
            $output .= '\'' . addslashes((string)$value) . '\'';
        }
        $output .= ',' . \PHP_EOL;
        return $output;
    }

    /**
     * Converts an item's name to an appropriate ID for Mongo storage.
     *
     * Changes non-latin characters with their latin equivalents, removes
     * punctuation, and lowercases the name. For example, changes "Armanté
     * Dress" to "armante-dress" and "Form Fitting, Shirt (2050)" to
     * "form-fitting-shirt-2050".
     */
    protected function nameToId(string $name): string
    {
        $name = (string)iconv('UTF-8', 'us-ascii//TRANSLIT//IGNORE', $name);
        $name = str_replace(
            ['(', ')', 'Rating ', '\'', ',', ':', ' ', '/'],
            ['', '', '', '', '', '', '-', '-'],
            $name
        );
        return strtolower($name);
    }
}
