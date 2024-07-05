<?php

declare(strict_types=1);

namespace App\Console\Commands;

use GitElephant\Repository;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Str;
use Modules\Shadowrun5e\Models\ForceTrait;
use Modules\Shadowrun5e\Models\VehicleModificationSlotType;
use Modules\Shadowrun5e\Models\VehicleModificationType;
use RuntimeException;
use SimpleXMLElement;
use Throwable;
use ValueError;

use function addslashes;
use function array_diff;
use function array_merge;
use function array_reverse;
use function count;
use function explode;
use function file_exists;
use function file_put_contents;
use function iconv;
use function is_dir;
use function ksort;
use function min;
use function mkdir;
use function number_format;
use function simplexml_load_file;
use function sprintf;
use function str_replace;
use function strtolower;
use function ucfirst;

use const PHP_EOL;

/**
 * @codeCoverageIgnore
 * @psalm-suppress InvalidArgument
 * @psalm-suppress UnusedClass
 */
class ImportChummerData extends Command implements Isolatable
{
    use ForceTrait;

    protected const MAX_VEHICLE_BODY = 36;
    protected const SLOTS_PER_STANDARD_ARMOR = 2;
    protected const SLOTS_PER_CONCEALED_ARMOR = 3;

    /**
     * List of valid types that can be imported.
     * @var array<int, string>
     */
    protected const DATA_TYPES = [
        'armor',
        'augmentations',
        'complex-forms',
        'critter-powers',
        'gear',
        'resonance-echoes',
        'vehicle-modifications',
        'vehicles',
        'weapons',
    ];

    /**
     * Mapping of Chummer source codes to Commlink ruleset names.
     * @var array<string, null|string>
     */
    protected array $source_map;

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

    public function __construct()
    {
        parent::__construct();
        $filename = config('shadowrun5e.data_path') . 'chummer-sources.php';
        /** @psalm-suppress UnresolvableInclude */
        $this->source_map = require $filename;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (true === $this->option('list-types')) {
            $this->listTypes();
            return self::SUCCESS;
        }

        $this->info('Creating Commlink data files from Chummer 5 data');

        // Don't output warnings loading bad XML files, we'll handle it.
        libxml_use_internal_errors(true);

        // Only allow one run at a time.
        $this->input->setOption('isolated', true);

        $this->validateOptions();

        $this->updateChummerRepository();

        $types = (array)$this->option('type');
        if (0 === count($types)) {
            $types = self::DATA_TYPES;
        }

        foreach ($types as $type) {
            $function = 'process' . ucfirst(str_replace('-', '', (string)$type));
            // @phpstan-ignore-next-line
            $this->$function();
        }

        $this->info('Creating Commlink data: ' . $this->outputDir);
        return self::SUCCESS;
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
            $this->line(sprintf(
                'Cloning Chummer 5 repository: %s',
                $this->chummerRepository
            ));
            mkdir($this->chummerRepository);
            Repository::createFromRemote(
                git: 'https://github.com/chummer5a/chummer5a.git',
                repositoryPath: $this->chummerRepository,
            );
            return;
        }

        if (true === $this->option('skip-pull')) {
            $this->line('Skipping git update on Chummer repository');
            return;
        }

        $this->line(sprintf(
            'Updating Chummer 5 repository: %s',
            $this->chummerRepository
        ));
        $repo = new Repository($this->chummerRepository);
        try {
            $repo->pull();
        } catch (Throwable) {
            throw new RuntimeException('Failed to pull repository');
        }
    }

    /**
     * Try to load the requested file as XML.
     */
    protected function loadXml(string $file): SimpleXMLElement
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

    protected function processArmor(): void
    {
        $data = $this->loadXml(
            $this->chummerRepository . '/Chummer/data/armor.xml'
        );

        /** @var array<string, array<string, array<string, int>|int|string>> */
        $armors = [];
        $bar = $this->output->createProgressBar(count($data->armors->armor));
        $bar->setFormat('  Armor            %current%/%max% [%bar%] %percent%');
        $bar->start();

        foreach ($data->armors->armor as $armor) {
            if (null === $this->source_map[(string)$armor->source]) {
                continue;
            }

            $id = $this->nameToId((string)$armor->name);
            $armorItem = [
                'availability' => $this->cleanAvailability($armor),
                'capacity' => (int)$armor->armorcapacity,
                'chummer-id' => (string)$armor->id,
                'cost' => (int)$armor->cost,
                'description' => '',
                'id' => $id,
                'name' => (string)$armor->name,
                'page' => (int)$armor->page,
                'rating' => (int)$armor->armor,
                'ruleset' => $this->source_map[(string)$armor->source],
            ];
            if (isset($armor->bonus, $armor->bonus->limitmodifier)) {
                $effect = strtolower(
                    (string)$armor->bonus->limitmodifier->limit
                );
                $armorItem['effects'] = [
                    $effect => (int)$armor->bonus->limitmodifier->value,
                ];
            }
            $armors[$id] = $armorItem;
            $bar->advance();
        }
        $bar->setFormat('  Armor            %current%/%max% [%bar%] -- ' . count($armors) . ' armor');
        $bar->finish();
        $this->newLine();
        // @psalm-suppress InvalidArgument
        $this->writeFile('armor.php', $armors);
    }

    protected function processAugmentations(): void
    {
        $augmentations = [];

        $data = $this->loadXml(
            $this->chummerRepository . '/Chummer/data/bioware.xml'
        );
        $count = count($data->biowares->bioware);
        $bar = $this->output->createProgressBar($count);
        $bar->setFormat('  Augmentations    %current%/%max% [%bar%] %percent% -- %message%');
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
            '  Augmentations    %current%/%max% [%bar%] -- '
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
        SimpleXMLElement $aug,
        string $type,
        array &$augmentations
    ): void {
        if (null === $this->source_map[(string)$aug->source]) {
            return;
        }

        $id = $this->nameToId((string)$aug->name);
        $augmentation = [
            'chummer-id' => (string)$aug->id,
            'description' => '',
            'id' => $id,
            'name' => (string)$aug->name,
            'page' => (int)$aug->page,
            'ruleset' => $this->source_map[(string)$aug->source],
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
        $bar->setFormat(
            '  Complex forms      %current%/%max% [%bar%] %percent%'
        );
        $bar->start();
        $forms = [];
        foreach ($data->complexforms->complexform as $rawForm) {
            if (null === $this->source_map[(string)$rawForm->source]) {
                $bar->advance();
                continue;
            }

            $name = (string)$rawForm->name;
            $id = $this->nameToId($name);
            $form = [
                'chummer-id' => (string)$rawForm->id,
                'description' => '',
                'duration' => (string)$rawForm->duration,
                'fade' => (string)$rawForm->fv,
                'id' => $id,
                'name' => $name,
                'page' => (int)$rawForm->page,
                'ruleset' => $this->source_map[(string)$rawForm->source],
                'target' => (string)$rawForm->target,
            ];

            if (
                Str::startsWith($name, 'Infusion')
                || Str::startsWith($name, 'Diffusion')
            ) {
                foreach ($matrixAttributes as $attribute) {
                    /** @var string */
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

            $forms[$id] = $form;
            $bar->advance();
        }
        $bar->setFormat(
            '  Complex forms      %current%/%max% [%bar%] -- ' . count($forms)
                . ' complex forms'
        );
        $bar->finish();
        $this->newLine();
        // @psalm-suppress InvalidArgument
        $this->writeFile('complex-forms.php', $forms);
    }

    protected function processCritterpowers(): void
    {
        $data = $this->loadXml(
            $this->chummerRepository . '/Chummer/data/critterpowers.xml'
        );

        $bar = $this->output->createProgressBar(count($data->powers->power));
        $bar->setFormat('  Critter powers   %current%/%max% [%bar%] %percent%');
        $bar->start();
        $powers = [];
        foreach ($data->powers->power as $rawPower) {
            if (null === $this->source_map[(string)$rawPower->source]) {
                continue;
            }

            if (isset($rawPower->hide)) {
                continue;
            }

            $id = $this->nameToId((string)$rawPower->name);
            $powers[$id] = [
                'action' => (string)$rawPower->action,
                'chummer-id' => (string)$rawPower->id,
                'description' => '',
                'duration' => (string)$rawPower->duration,
                'id' => $id,
                'name' => (string)$rawPower->name,
                'page' => (int)$rawPower->page,
                'range' => (string)$rawPower->range,
                'ruleset' => $this->source_map[(string)$rawPower->source],
                'type' => (string)$rawPower->type,
            ];
        }
        $bar->setFormat(
            '  Critter powers   %current%/%max% [%bar%] -- ' . count($powers)
                . ' powers'
        );
        $bar->finish();
        $this->newLine();
        // @psalm-suppress InvalidArgument
        $this->writeFile('critter-powers.php', $powers);
    }

    protected function processGear(): void
    {
        // Gear that needs to have additional processing for matrix attributes
        // (attack, sleaze, data processing, firewall, and device rating)
        $matrixDevices = [
            'Commlinks',
            'Cyberdecks',
            'Cyberterminals',
            'Rigger Command Consoles',
        ];

        // Categories that either aren't supported by Commlink, or are
        // supported under a different type.
        $skippedCategories = [
            'Ammunition',
            'Custom Cyberdeck Attributes',
            'Electronic Modification',
            'Formulae',
            'Magical Compounds',
        ];

        // Individual items that aren't supported by Commlink.
        $skippedItems = [
            '73b55822-dfb8-48f5-8ff8-37ef498ab9ef', // Living persona
            '9218a0ea-f1c5-4532-bbe0-25c10e97f0b9', // Remove control deck
            'd63eb841-7b15-4539-9026-b90a4924aeeb', // Custom commlink
        ];

        $data = $this->loadXml(
            $this->chummerRepository . '/Chummer/data/gear.xml'
        );

        $bar = $this->output->createProgressBar(count($data->gears->gear));
        $bar->setFormat(
            '  Gear           %current%/%max% [%bar%] %percent%'
        );
        $bar->start();
        $gears = [];
        foreach ($data->gears->gear as $rawGear) {
            $category = (string)$rawGear->category;
            $chummerId = (string)$rawGear->id;
            $name = (string)$rawGear->name;
            $subname = null;
            if (
                null === $this->source_map[(string)$rawGear->source]
                || in_array($category, $skippedCategories, true)
                || in_array($chummerId, $skippedItems, true)
                || ('Foci' === $category && Str::contains($name, 'Individualized'))
            ) {
                $bar->advance();
                continue;
            }
            if (Str::contains($name, 'Focus')) {
                if (Str::contains($name, '2050')) {
                    $bar->advance();
                    continue;
                }

                if (Str::contains($name, ', ')) {
                    $subname = Str::after($name, ', ');
                    $name = Str::before($name, ', ');
                }
                $name = sprintf('%s - %s', ...array_reverse(explode(' ', $name)));
            }

            if (null !== $subname) {
                $id = $this->nameToId(
                    str_replace(' - ', ' ', $name) . ' ' . $subname
                );
            } else {
                $id = $this->nameToId(str_replace(' - ', ' ', $name));
            }

            $gear = [
                'availability' => $this->cleanAvailability($rawGear),
                'chummer-id' => $chummerId,
                'cost' => (int)$rawGear->cost,
                'description' => '',
                'id' => $id,
                'name' => $name,
                'page' => (int)$rawGear->page,
                'ruleset' => $this->source_map[(string)$rawGear->source],
            ];
            if (null !== $subname) {
                $gear['subname'] = $subname;
            }

            if (in_array($category, $matrixDevices, true)) {
                $gear = array_merge($gear, $this->addMatrixAttributes($rawGear));
            }

            if (!isset($rawGear->rating)) {
                $gears[$id] = $gear;
                $bar->advance();
                continue;
            }

            $maxRating = min((int)$rawGear->rating, 12);
            for ($rating = 1; $rating <= $maxRating; $rating++) {
                $gear['rating'] = $rating;
                $gear['availability']
                    = $this->cleanAvailability($rawGear, $rating);
                $cost = (string)$rawGear->cost;
                if (Str::contains($cost, 'FixedValues')) {
                    $gear['cost'] = $this->fixedValuesToArray($cost)[$rating];
                } else {
                    $gear['cost']
                        = $this->calculateCost((string)$rawGear->cost, $rating);
                }
                $gears[$id . '-' . $rating] = $gear;
            }
            $bar->advance();
        }
        $bar->setFormat(
            '  Gear           %current%/%max% [%bar%] -- ' . count($gears)
                . ' gear items'
        );
        $bar->finish();
        $this->newLine();
        $this->writeFile('gear.php', $gears);
    }

    /**
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     * @return array<string, array<int, int|string>|int|string>
     */
    protected function addMatrixAttributes(SimpleXMLElement $gear): array
    {
        $matrix = [];
        if ('' !== (string)$gear->attributearray) {
            $matrix['attributes'] = explode(',', (string)$gear->attributearray);
        } else {
            $matrix['attributes'] = [
                'attack' => (int)$gear->attack,
                'data-processing' => (int)$gear->dataprocessing,
                'firewall' => (int)$gear->firewall,
                'sleaze' => (int)$gear->sleaze,
            ];
        }
        if (0 !== (int)$gear->devicerating) {
            $matrix['rating'] = (int)$gear->devicerating;
        } elseif (0 !== (int)$gear->rating) {
            $matrix['rating'] = (int)$gear->rating;
        }
        if (0 !== (int)$gear->programs) {
            $matrix['programs'] = (int)$gear->programs;
        }

        // @phpstan-ignore-next-line
        $matrix['container-type'] = [match ((string)$gear->category) {
            'Commlinks' => 'commlink',
            'Cyberdecks' => 'cyberdeck',
            'Cyberterminals' => 'commlink',
            'Rigger Command Consoles' => 'rcc',
        }];
        // @phpstan-ignore-next-line
        return $matrix;
    }

    protected function processResonanceEchoes(): void
    {
        $data = $this->loadXml(
            $this->chummerRepository . '/Chummer/data/echoes.xml'
        );

        $echoes = [];
        $bar = $this->output->createProgressBar(count($data->echoes->echo));
        $bar->setFormat('  Echoes           %current%/%max% [%bar%] %percent%');
        $bar->start();

        foreach ($data->echoes->echo as $rawEcho) {
            $id = $this->nameToId((string)$rawEcho->name);
            $echoes[$id] = [
                'chummer-id' => $rawEcho->id,
                'description' => '',
                'id' => $id,
                'limit' => (int)($rawEcho->limit ?? 1),
                'name' => $rawEcho->name,
                'page' => (int)$rawEcho->page,
                'ruleset' => $this->source_map[(string)$rawEcho->source],
            ];
            $bar->advance();
        }
        $bar->setFormat(
            '  Echoes             %current%/%max% [%bar%] -- ' . count($echoes)
                . ' echoes'
        );
        $bar->finish();
        $this->newLine();
        $this->writeFile('resonance-echoes.php', $echoes);
    }

    protected function processWeapons(): void
    {
        $data = $this->loadXml(
            $this->chummerRepository . '/Chummer/data/weapons.xml'
        );

        $weapons = [];
        $bar = $this->output->createProgressBar(count($data->weapons->weapon));
        $bar->setFormat('  Weapons          %current%/%max% [%bar%] %percent%');
        $bar->start();
        foreach ($data->weapons->weapon as $rawWeapon) {
            if (null === $this->source_map[(string)$rawWeapon->source]) {
                continue;
            }

            $id = $this->nameToId((string)$rawWeapon->name);
            $weapon = [
                'accuracy' => (int)$rawWeapon->accuracy,
                'armor-piercing' => (int)$rawWeapon->accuracy,
                'chummer-id' => (string)$rawWeapon->id,
                'class' => (string)$rawWeapon->category,
                'damage' => (string)$rawWeapon->damage,
                'description' => '',
                'id' => $id,
                'name' => (string)$rawWeapon->name,
                'page' => (int)$rawWeapon->page,
                'ruleset' => $this->source_map[(string)$rawWeapon->source],
            ];

            if (!isset($rawWeapon->maxrating)) {
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
        $bar->setFormat(
            '  Weapons          %current%/%max% [%bar%] -- ' . count($weapons)
                . ' weapons'
        );
        $bar->finish();
        $this->newLine();
        $this->writeFile('weapons.php', $weapons);
    }

    protected function processVehicles(): void
    {
        $data = $this->loadXml(
            $this->chummerRepository . '/Chummer/data/vehicles.xml'
        );

        $vehicles = [];
        $bar = $this->output->createProgressBar(count($data->vehicles->vehicle));
        $bar->setFormat('  Vehicles         %current%/%max% [%bar%] %percent%');
        $bar->start();
        foreach ($data->vehicles->vehicle as $rawVehicle) {
            if (null === $this->source_map[(string)$rawVehicle->source]) {
                continue;
            }

            $id = $this->nameToId((string)$rawVehicle->name);
            $vehicle = [
                'acceleration' => (int)$rawVehicle->accel,
                'armor' => (int)$rawVehicle->armor,
                'availability' => $this->cleanAvailability($rawVehicle),
                'body' => (int)$rawVehicle->body,
                'category' => (string)$rawVehicle->category,
                'chummer-id' => (string)$rawVehicle->id,
                'cost' => (int)$rawVehicle->cost,
                'description' => '',
                'handling' => (int)$rawVehicle->handling,
                'id' => $id,
                'name' => (string)$rawVehicle->name,
                'page' => (int)$rawVehicle->page,
                'pilot' => (int)$rawVehicle->pilot,
                'ruleset' => $this->source_map[(string)$rawVehicle->source],
                'seats' => (int)$rawVehicle->seats,
                'sensor' => (int)$rawVehicle->sensor,
                'speed' => (int)$rawVehicle->speed,
            ];
            $vehicles[$id] = $vehicle;
            $bar->advance();
        }
        $bar->setFormat(
            '  Vehicles         %current%/%max% [%bar%] -- ' . count($vehicles)
                . ' vehicles'
        );
        $bar->finish();
        $this->newLine();
        $this->writeFile('vehicles.php', $vehicles);
    }

    protected function processVehicleModifications(): void
    {
        $data = $this->loadXml(
            $this->chummerRepository . '/Chummer/data/vehicles.xml'
        );

        $vehicleMods = [];
        $bar = $this->output->createProgressBar(count($data->mods->mod));
        $bar->setFormat('  Vehicle Mods     %current%/%max% [%bar%] %percent%');
        $bar->start();
        foreach ($data->mods->mod as $rawMod) {
            if (null === $this->source_map[(string)$rawMod->source]) {
                continue;
            }
            if (isset($rawMod->hide)) {
                continue;
            }
            if ('Model-Specific' === (string)$rawMod->category) {
                continue;
            }

            $name = (string)$rawMod->name;
            $id = $this->nameToId((string)$rawMod->name);
            $slotType = strtolower((string)$rawMod->category);
            if ('powertrain' === $slotType) {
                $slotType = 'power-train';
            }
            try {
                $slotType = VehicleModificationSlotType::from($slotType);
            } catch (ValueError) {
                $bar->advance();
                continue;
            }

            $mod = [
                'availability' => $this->cleanAvailability($rawMod),
                'chummer-id' => (string)$rawMod->id,
                'description' => '',
                'id' => $id,
                'name' => $name,
                'page' => (int)$rawMod->page,
                'ruleset' => $this->source_map[(string)$rawMod->source],
                'slot-type' => $slotType,
                'type' => VehicleModificationType::VehicleModification,
            ];

            $maxRating = (string)$rawMod->rating;
            if ('body' === $maxRating) {
                // Armor has a max of the vehicle's body rating, which may be
                // as high as 36 for the Lurssen Mobius. But each armor takes up
                // two or three slots depending on the type.
                if ('Armor (Standard)' === $name) {
                    $maxRating = self::MAX_VEHICLE_BODY / self::SLOTS_PER_STANDARD_ARMOR;
                } elseif ('Armor (Concealed)' === $name) {
                    $maxRating = self::MAX_VEHICLE_BODY / self::SLOTS_PER_CONCEALED_ARMOR;
                }
            }

            $maxRating = (int)$maxRating;
            $slots = (string)$rawMod->slots;
            $cost = (string)$rawMod->cost;
            if (is_numeric($slots) && 0 === $maxRating) {
                $mod['cost'] = (int)$cost;
                $mod['slots'] = (int)$slots;
                $vehicleMods[$this->nameToId((string)$rawMod->name)] = $mod;
                $bar->advance();
                continue;
            }
            if (is_numeric($slots)) {
                $mod['slots'] = (int)$slots;
                for ($rating = 1; $rating <= $maxRating; $rating++) {
                    $mod['availability'] = $this->cleanAvailability($rawMod, $rating);
                    $mod['cost']
                        = $this->calculateValueFromFormula($cost, $rating);
                    $mod['rating'] = $rating;
                    $vehicleMods[$this->nameToId($name) . '-' . $rating] = $mod;
                }
                $bar->advance();
                continue;
            }

            if (0 === $maxRating) {
                // Gecko Tips and Gliding System need to be added manually.
                $bar->advance();
                continue;
            }

            if (Str::contains($slots, 'Rating')) {
                for ($rating = 1; $rating <= $maxRating; $rating++) {
                    $mod['availability'] = $this->cleanAvailability($rawMod, $rating);
                    $mod['cost']
                        = $this->calculateValueFromFormula($cost, $rating);
                    $mod['rating'] = $rating;
                    $mod['slots']
                        = $this->calculateValueFromFormula($slots, $rating);
                    $vehicleMods[$this->nameToId($name) . '-' . $rating] = $mod;
                }
                $bar->advance();
                continue;
            }

            if (Str::contains($slots, 'FixedValues')) {
                $slots = $this->fixedValuesToArray($slots);
                $costs = $this->fixedValuesToArray($cost);
                foreach ($slots as $rating => $slot) {
                    $mod['availability'] = $this->cleanAvailability($rawMod, $rating);
                    $mod['cost'] = $costs[$rating];
                    $mod['rating'] = $rating;
                    $mod['slots'] = (int)$slot;
                    $vehicleMods[$this->nameToId($name) . '-' . $rating] = $mod;
                }
                $bar->advance();
                continue;
            }

            $vehicleMods[$id] = $mod;
            $bar->advance();
        }
        $bar->setFormat(
            '  Vehicle mods     %current%/%max% [%bar%] -- '
                . count($vehicleMods) . ' vehicle modifications'
        );
        $bar->finish();
        $this->newLine();
        $this->writeFile(
            'vehicle-modifications.php',
            $vehicleMods,
            [
                'Vehicle',
                'VehicleModificationSlotType',
                'VehicleModificationType',
            ],
        );
    }

    /**
     * Several values in Chummer's data appear like
     * "FixedValues(500,1000,2500,5000)" which means the value for whatever
     * attribute at rating 1 is 500, rating 2 is 1000, etc. This returns an
     * array starting with key 1 for the values. Values are returned as strings
     * since many data points are formulas that need further processing, though
     * some are simple integers.
     * @return array<int, string>
     */
    protected function fixedValuesToArray(string $values): array
    {
        $values = Str::between($values, '(', ')');
        $values = array_merge([0 => null], explode(',', $values));
        unset($values[0]);
        // @phpstan-ignore-next-line
        return $values;
    }

    /**
     * Several different attributes in Chummer's data are based on the rating,
     * and write it in different ways. Given a string like "Rating * 3000",
     * replaces the word "Rating" with the numerical rating and calculates the
     * formula's result.
     */
    protected function calculateValueFromFormula(
        string $formula,
        int $rating
    ): int {
        $formula = Str::replace('Rating', 'Q', $formula);
        $formula = Str::replace(' ', '', $formula);
        $formula = Str::replace('{', '', $formula);
        /** @var string */
        $formula = Str::replace('}', '', $formula);
        return $this->convertFormula($formula, 'Q', $rating);
    }

    /**
     * Given a Chummer item, return the availability for Commlink.
     *
     * If the availability is zero, replace with an empty string. If it's
     * a rating-based availability, use the rating to figure out the rating
     * code.
     */
    protected function cleanAvailability(
        SimpleXMLElement $item,
        ?int $rating = null
    ): string {
        $availability = (string)$item->avail;

        if (Str::contains($availability, 'Rating')) {
            $availability = str_replace('(Rating)', 'Rating', $availability);
            $formula = Str::between($availability, '(', ')');
            $restriction = '';
            if (Str::contains($availability, ')')) {
                $restriction = Str::after($availability, ')');
            }
            $availability = $this->calculateValueFromFormula($formula, (int)$rating)
                . $restriction;
            if (Str::contains($availability, '+')) {
                return (string)$this->calculateValueFromFormula($availability, 0);
            }
            return $availability;
        }
        if (Str::contains($availability, 'FixedValues') && null !== $rating) {
            return $this->fixedValuesToArray($availability)[$rating]
                . Str::after($availability, ')');
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
        $formula = Str::replace('(Rating)', 'Rating', $cost);
        $formula = Str::replace('Rating', 'Q', $formula);
        $formula = Str::replace(' ', '', $formula);
        $formula = Str::replace('{', '', $formula);
        /** @var string */
        $formula = Str::replace('}', '', $formula);
        if (Str::contains($formula, '(')) {
            $temp = (string)$this->convertFormula(
                Str::between($formula, '(', ')'),
                'Q',
                $rating
            );
            /** @var string */
            $formula = Str::replace(
                '(' . Str::between($formula, '(', ')') . ')',
                $temp,
                $formula
            );
        }
        return $this->convertFormula($formula, 'Q', $rating);
    }

    /**
     * Given a rating-based essence formula, return the essence cost for the
     * standard-grade of the 'ware.
     */
    protected function calculateEssence(string $essence, int $rating): float
    {
        /** @var string */
        $formula = Str::replace('Rating', 'R', $essence);
        /** @var string */
        $formula = Str::replace(' ', '', $formula);
        $cost = (float)Str::after($formula, '*') * 100;
        /** @var string */
        $formula = Str::replace(Str::after($formula, '*'), (string)$cost, $formula);
        $cost = $this->convertFormula($formula, 'R', $rating) / 100;
        return $cost;
    }

    /**
     * Writes the given data to a file as a PHP array.
     * @param array<string, mixed> $data
     * @param ?array<int, string> $imports Use statements to add to data file
     */
    protected function writeFile(
        string $file,
        array $data,
        ?array $imports = null
    ): void {
        $output = '<?php' . PHP_EOL
            . PHP_EOL
            . 'declare(strict_types=1);' . PHP_EOL
            . PHP_EOL;
        if (null !== $imports) {
            foreach ($imports as $import) {
                $output .= 'use App\\Models\\Shadowrun5e\\' . $import . ';' . PHP_EOL;
            }
            $output .= PHP_EOL;
        }
        $output .= 'return [' . PHP_EOL;

        ksort($data);
        foreach ($data as $id => $item) {
            ksort($item);
            $output .= '    \'' . $id . '\' => [' . PHP_EOL;
            foreach ($item as $key => $value) {
                $output .= $this->writeLine(2, $key, $value);
            }
            $output .= '    ],' . PHP_EOL;
        }

        $output .= '];' . PHP_EOL;
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
        int|string $key,
        mixed $value
    ): string {
        $padding = str_repeat(' ', $level * 4);
        $output = $padding . '\'' . $key . '\' => ';
        if (is_array($value)) {
            $output .= '[' . PHP_EOL;
            foreach ($value as $subKey => $subValue) {
                $output .= $this->writeLine($level + 1, $subKey, $subValue);
            }
            $output .= $padding . ']';
        } elseif (is_numeric($value) && 'availability' !== $key) {
            $output .= $value;
        } elseif ($value instanceof VehicleModificationSlotType) {
            $output .= 'VehicleModificationSlotType::' . $value->name;
        } elseif ($value instanceof VehicleModificationType) {
            $output .= 'VehicleModificationType::' . $value->name;
        } else {
            $output .= '\'' . addslashes((string)$value) . '\'';
        }
        $output .= ',' . PHP_EOL;
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
            ['(', ')', 'Rating ', '\'', ',', ':', ' ', '/', '"', '[', ']'],
            ['', '', '', '', '', '', '-', '-', '', '', ''],
            $name
        );
        return strtolower($name);
    }
}
