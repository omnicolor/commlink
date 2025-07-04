<?php

declare(strict_types=1);

namespace App\Services\HeroLab;

use App\Services\ConverterInterface;
use ErrorException;
use Modules\Shadowrun5e\Models\ActiveSkill;
use Modules\Shadowrun5e\Models\AdeptPower;
use Modules\Shadowrun5e\Models\Armor;
use Modules\Shadowrun5e\Models\Augmentation;
use Modules\Shadowrun5e\Models\Gear;
use Modules\Shadowrun5e\Models\GearFactory;
use Modules\Shadowrun5e\Models\Metamagic;
use Modules\Shadowrun5e\Models\PartialCharacter;
use Modules\Shadowrun5e\Models\Quality;
use Modules\Shadowrun5e\Models\SkillGroup;
use Modules\Shadowrun5e\Models\Spell;
use Modules\Shadowrun5e\Models\Vehicle;
use Modules\Shadowrun5e\Models\VehicleModification;
use Modules\Shadowrun5e\Models\Weapon;
use Modules\Shadowrun5e\Models\WeaponModification;
use Override;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

use function array_key_exists;
use function array_merge;
use function array_unique;
use function array_walk;
use function count;
use function explode;
use function file_get_contents;
use function implode;
use function in_array;
use function is_array;
use function mkdir;
use function next;
use function rmdir;
use function simplexml_load_file;
use function sort;
use function sprintf;
use function str_contains;
use function str_replace;
use function str_starts_with;
use function strtolower;
use function substr;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

use const DIRECTORY_SEPARATOR;
use const LIBXML_NOERROR;
use const PHP_EOL;

/**
 * Importer class for Hero Lab Shadowrun 5E profiles.
 */
class Shadowrun5eConverter implements ConverterInterface
{
    /**
     * Character being converted.
     */
    protected PartialCharacter $character;

    /**
     * Directory the portfolio was extracted to.
     */
    protected string $directory;
    /** @var array<string, array<int, string>> */
    protected array $errors = [];

    /**
     * Map of Hero Lab armor names to Commlink IDs or null for those ignored.
     * @var array<string, ?string>
     */
    protected array $mapArmor = [
        'Natural Armor' => null,
        'Licensed Team Jersey' => 'armored-team-jerseys-licensed',
        'Unlicensed Team Jersey' => 'armored-team-jerseys-unlicensed',
    ];

    /**
     * Map of Hero Lab gear names to Commlink IDs.
     * @var array<string, ?string>
     */
    protected array $mapGear = [
        'Alphasprin' => 'drug-alphasprin',
        'Antidote Patch' => 'patch-antidote-1',
        'Certified Credstick, Ebony' => 'credstick-ebony',
        'Certified Credstick, Gold' => 'credstick-gold',
        'Certified Credstick, Silver' => 'credstick-silver',
        'Certified Credstick, Standard' => 'credstick-standard',
        'Deepweed' => 'drug-deepweed',
        'Flashlight, Thermographic' => 'flashlight-infrared',
        'Living Persona' => null,
        'Jazz' => 'drug-jazz',
        'Kamikaze' => 'drug-kamikaze',
        'Micro-Tranceiver' => 'micro-transceiver',
        'Novacoke' => 'drug-novacoke',
        'Reagents, tainted raw (dram)' => 'reagents',
        'Sim Module' => null,
        'Security Tags' => 'tag-security',
        'Sober Time' => 'drug-sober-time',
        'Standard Tags' => 'tag-standard',
        'Stealth Tags' => 'tag-stealth',
        'Stim Patch' => 'patch-stim-1',
        'Tranq Patch' => 'patch-tranq-1',
        'Trauma Patch' => 'patch-trauma',
    ];

    /**
     * Map of Hero Lab metamagic names to Commlink IDs.
     * @var array<string, ?string>
     */
    protected array $mapMetamagic = [
        'Centering +1dicepool' => 'centering',
    ];

    /**
     * Map of Hero Lab quality names to Commlink IDs or null for those ignored.
     * @var array<string, ?string>
     */
    protected array $mapQualities = [
        'Adept' => null,
        'Insomnia (Half-Speed Recovery) (7dicepool vs. 4)' => 'insomnia-1',
        'Reduced (hearing)' => 'reduced-sense-hearing',
        'Reduced (sight)' => 'reduced-sense-sight',
        'Subtle Ground Craft Pilot: Pilot Ground Craft' => 'subtle-pilot-ground-craft',
        'Technomancer' => null,
    ];

    /**
     * Map of Hero Lab weapons to Commlink IDs.
     * @var array<string, ?string>
     */
    protected array $mapWeapons = [
        'AK-98 Grenade Launcher' => null,
        'Defiance T-250 (short-barrel version)' => 'defiance-t-250-short',
        'Unarmed Strike' => null,
    ];

    /**
     * Map of Hero Lab vehicles to Commlink IDs.
     * @var array<string, ?string>
     */
    protected array $mapVehicles = [
        'GMC-NISSAN DOBERMAN' => 'gm-nissan-doberman',
        'LUFTSHIFFBAU PERSONAL ZEPPELIN LZP-2070' => 'luftshiffbau-lzp-2070',
    ];

    /**
     * @var array<string, mixed>
     */
    protected array $mapVehicleModifications = [
        'Weapon Mount (Flexible, External, Remote)' => [
            'id' => 'weapon-mount-standard',
            'modifications' => [
                'control-remote',
                'flexibility-flexible',
                'visibility-external',
            ],
        ],
    ];

    /**
     * Hero portfolio.
     */
    protected SimpleXMLElement $xml;

    /**
     * Additional information about the hero.
     */
    protected SimpleXMLElement $xmlMeta;

    /**
     * Additional character sheets attached to the hero.
     */
    protected SimpleXMLElement $minions;

    /**
     * @throws RuntimeException
     */
    public function __construct(string $filename)
    {
        $this->character = new PartialCharacter();
        $this->createTemporaryDirectory();
        $this->extractArchive($filename);
        $this->parseFiles();
    }

    public function __destruct()
    {
        $this->cleanup();
    }

    /**
     * Clean up any temporary files left over.
     */
    protected function cleanup(): void
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->directory,
                RecursiveDirectoryIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
                continue;
            }
            unlink($file->getRealPath());
        }

        rmdir($this->directory);
    }

    /**
     * Create a temporary directory to extract the portfolio to.
     */
    protected function createTemporaryDirectory(): void
    {
        $this->directory = (string)tempnam(sys_get_temp_dir(), 'commlink-');
        unlink($this->directory);
        mkdir($this->directory);
    }

    /**
     * Extract the portfolio to the temporary directory.
     * @throws RuntimeException
     */
    protected function extractArchive(string $filename): void
    {
        $zip = new ZipArchive();
        $res = $zip->open($filename, ZipArchive::RDONLY);
        if (true !== $res) {
            switch ($res) {
                case ZipArchive::ER_NOZIP:
                    $this->cleanup();
                    throw new RuntimeException('Portfolio is not valid');
                case ZipArchive::ER_NOENT:
                    $this->cleanup();
                    throw new RuntimeException('Portfolio not found');
                default:
                    $this->cleanup();
                    throw new RuntimeException(sprintf(
                        'Opening portfolio failed with unknown code: %d',
                        $res
                    ));
            }
        }

        // @codeCoverageIgnoreStart
        if (false === $zip->extractTo($this->directory)) {
            $this->cleanup();
            throw new RuntimeException(
                'Unable to extract Portfolio to temporary directory'
            );
        }
        // @codeCoverageIgnoreEnd

        $zip->close();
    }

    /**
     * Parse the XML and text files from the portfolio.
     * @throws RuntimeException
     */
    protected function parseFiles(): void
    {
        // Load the index file.
        $index = implode(DIRECTORY_SEPARATOR, [$this->directory, 'index.xml']);
        try {
            $index = simplexml_load_file($index);
            if (false === $index) {
                throw new ErrorException();
            }
        } catch (ErrorException) {
            $this->cleanup();
            throw new RuntimeException('Portfolio metadata is invalid');
        }
        if ('Shadowrun (5th)' !== (string)$index->game['name']) {
            $this->cleanup();
            throw new RuntimeException(
                'The portfolio isn\'t a Shadowrun 5th edition character'
            );
        }
        $character = $index->characters[0]->character;
        foreach ($character->statblocks->children() as $statblock) {
            if ('xml' !== (string)$statblock['format']) {
                continue;
            }
            $file = sprintf(
                '%s%s%s%s%s',
                $this->directory,
                DIRECTORY_SEPARATOR,
                $statblock['folder'],
                DIRECTORY_SEPARATOR,
                $statblock['filename'],
            );
            $xml = simplexml_load_file(filename: $file, options: LIBXML_NOERROR);
            if (false === $xml) {
                $this->cleanup();
                throw new RuntimeException('Failed to load Portfolio stats');
            }
            $this->xml = $xml->public->character;
            break;
        }
        $this->minions = $character->minions;

        // Load the meta file, containing priorities.
        $meta = sprintf(
            '%s%sherolab%slead%d.xml',
            $this->directory,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            $character['herolableadindex'],
        );

        try {
            $xml = simplexml_load_file($meta);
        } catch (ErrorException) {
            $this->cleanup();
            throw new RuntimeException('Portfolio metadata is invalid');
        }

        // @codeCoverageIgnoreStart
        if (false === $xml) {
            $this->cleanup();
            throw new RuntimeException('Failed to load Portfolio metadata');
        }
        // @codeCoverageIgnoreEnd
        $this->xmlMeta = $xml;
    }

    /**
     * Convert a name to something that might be a valid Commlink ID.
     */
    public function createIDFromName(string $name): string
    {
        return strtolower(str_replace(
            [' ', ':', ','],
            ['-', '', ''],
            $name
        ));
    }

    protected function parseAttributes(
        SimpleXMLElement $attributes
    ): Shadowrun5eConverter {
        $validAttributes = [
            'agility',
            'body',
            'charisma',
            'edge',
            'intuition',
            'logic',
            'magic',
            'reaction',
            'resonance',
            'strength',
            'willpower',
        ];
        foreach ($attributes as $rawAttribute) {
            $attribute = strtolower((string)$rawAttribute['name']);
            if (!in_array($attribute, $validAttributes, true)) {
                continue;
            }
            // @phpstan-ignore property.dynamicName
            $this->character->$attribute = (int)$rawAttribute['base'];
        }
        return $this;
    }

    protected function parseQualities(
        SimpleXMLElement $qualities
    ): Shadowrun5eConverter {
        $qualitiesArray = $this->character->qualities ?? [];
        foreach ($qualities->children() ?? [] as $rawQuality) {
            $name = (string)$rawQuality['name'];

            $rating = null;
            if (str_contains($name, '(')) {
                [$name, $rating] = explode(' (', $name);
                $rating = str_replace(')', '', $rating);
            }
            if (array_key_exists($name, $this->mapQualities)) {
                if (null === $this->mapQualities[$name]) {
                    continue;
                }
                try {
                    $quality = new Quality($this->mapQualities[$name]);
                } catch (RuntimeException $ex) {
                    $this->errors['qualities'] ??= [];
                    $this->errors['qualities'][] = $ex->getMessage();
                    continue;
                }
                $qualitiesArray[] = [
                    'id' => $quality->id,
                ];
                continue;
            }

            try {
                $quality = Quality::findByName($name);
                if (null !== $rating) {
                    $id = str_replace('1', $rating, $quality->id);
                    $qualitiesArray[] = ['id' => $id];
                } else {
                    $qualitiesArray[] = ['id' => $quality->id];
                }
                continue;
            } catch (RuntimeException) {
                // Ignore and try other ways of finding the Quality.
            }

            if (str_contains($name, ':')) {
                [$shortName, $extra] = explode(':', $name);
                try {
                    $found = Quality::findByName($shortName);
                    $qualitiesArray[] = [
                        'id' => $found->id,
                        'severity' => $extra,
                    ];
                    continue;
                } catch (RuntimeException) {
                    // Fall through to adding an error.
                }
            }
            $this->errors['qualities'] ??= [];
            $this->errors['qualities'][] = sprintf('Quality "%s" was not found.', $name);
        }
        $this->character->qualities = $qualitiesArray;
        return $this;
    }

    protected function parseSkillGroups(
        SimpleXMLElement $groups
    ): Shadowrun5eConverter {
        $skillGroups = [];
        foreach ($groups->children() ?? [] as $group) {
            $id = (string)$group['name'];
            $id = str_replace(' Group', '', $id);
            $id = strtolower($id);
            $level = (int)$group['base'];
            try {
                $group = new SkillGroup($id, $level);
            } catch (RuntimeException $ex) {
                $this->errors['skill-groups'] ??= [];
                $this->errors['skill-groups'][] = $ex->getMessage();
                continue;
            }
            $skillGroups[$group->id] = $group->level;
        }
        $this->character->skillGroups = $skillGroups;
        return $this;
    }

    /**
     * @return array<string, string>
     */
    protected function parseSpecializations(SimpleXMLElement $skill): array
    {
        if (!isset($skill->specialization)) {
            return [];
        }
        $specializations = explode(', ', (string)$skill->specialization['bonustext']);
        array_walk($specializations, function (string &$specialization): void {
            $specialization = substr($specialization, 0, -3);
        });
        return ['specialization' => implode(', ', $specializations)];
    }

    protected function parseActiveSkills(
        SimpleXMLElement $skills
    ): Shadowrun5eConverter {
        $skillsArray = [];
        foreach ($skills->children() ?? [] as $skill) {
            $id = $this->createIDFromName((string)$skill['name']);
            $level = (int)$skill['base'];
            if (0 === $level) {
                continue; // @codeCoverageIgnore
            }
            $specializations = $this->parseSpecializations($skill);

            try {
                $skillObject = new ActiveSkill($id, $level);
            } catch (RuntimeException $ex) {
                $this->errors['skills'] ??= [];
                $this->errors['skills'][] = $ex->getMessage();
                continue;
            }
            $skillsArray[] = array_merge(
                [
                    'id' => $skillObject->id,
                    'level' => $skillObject->level,
                ],
                $specializations,
            );
        }
        $this->character->skills = $skillsArray;
        return $this;
    }

    protected function parseKnowledgeSkills(
        SimpleXMLElement $skills,
        bool $isLanguage
    ): Shadowrun5eConverter {
        $category = $isLanguage ? 'language' : 'street';
        $skillsArray = $this->character->knowledgeSkills ?? [];
        foreach ($skills->children() ?? [] as $skill) {
            $name = (string)$skill['name'];
            $rating = (int)$skill['base'];

            $specializations = [];
            foreach ($skill->specialization as $spec) {
                $specializations[]
                    = str_replace(' +2', '', (string)$spec['bonustext']);
            }
            /**
             * Psalm thinks the array can never be empty, but not every skill
             * has a specialization.
             */
            if ([] !== $specializations) {
                $specializations = implode(',', $specializations);
            } else {
                $specializations = null;
            }

            $skillsArray[] = [
                'category' => $category,
                'name' => $name,
                'level' => $rating,
                'specialization' => $specializations,
            ];
        }
        $this->character->knowledgeSkills = $skillsArray;
        return $this;
    }

    /**
     * @TODO Add support for alchemical spells
     */
    protected function parseSpells(
        ?SimpleXMLElement $spells
    ): Shadowrun5eConverter {
        if (null === $spells || 0 === count($spells)) {
            return $this;
        }
        $magics = $this->character->magics;
        if (!is_array($magics)) {
            $magics = [];
        }
        $magics['spells'] = [];
        foreach ($spells->children() ?? [] as $spell) {
            $name = (string)$spell['name'];
            try {
                $magics['spells'][] = Spell::findByName($name)->id;
            } catch (RuntimeException $ex) {
                $this->errors['magic'] ??= [];
                $this->errors['magic'][] = $ex->getMessage();
                continue;
            }
        }
        $this->character->magics = $magics;
        return $this;
    }

    protected function parsePowers(
        ?SimpleXMLElement $powers
    ): Shadowrun5eConverter {
        if (null === $powers || 0 === count($powers)) {
            return $this;
        }
        $powersArray = [];
        foreach ($powers->children() ?? [] as $power) {
            $name = (string)$power['text'];
            $name = explode(' (', $name);
            $name = $name[0];
            $id = $this->createIDFromName($name);
            $rating = (int)$power['rating'];
            if (0 !== $rating) {
                $id = sprintf('%s-%d', $id, $rating);
            }
            $powersArray[] = (new AdeptPower($id))->id;
        }
        if (!is_array($this->character->magics)) {
            $this->character->magics = []; // @codeCoverageIgnore
        }
        $magics = $this->character->magics;
        $magics['powers'] = $powersArray;
        $this->character->magics = $magics;
        return $this;
    }

    protected function parseMetamagics(
        ?SimpleXMLElement $meta
    ): Shadowrun5eConverter {
        if (null === $meta || 0 === count($meta)) {
            return $this;
        }

        $metaArray = [];
        foreach ($meta->children() ?? [] as $metamagic) {
            $name = (string)$metamagic['name'];
            if (isset($this->mapMetamagic[$name])) {
                $metaArray[] = $this->mapMetamagic[$name];
                continue;
            }

            if (str_contains($name, '(')) {
                try {
                    $noParenthesisName = explode(' (', $name)[0];
                    $metaArray[] = Metamagic::findByName($noParenthesisName)->id;
                    continue;
                } catch (RuntimeException) { // @codeCoverageIgnore
                    // Ignore.
                }
            }

            try {
                $metaArray[] = Metamagic::findByName($name)->id;
            } catch (RuntimeException $ex) { // @codeCoverageIgnore
                $this->errors['magic'] ??= [];
                $this->errors['magic'][] = $ex->getMessage(); // @codeCoverageIgnore
                continue; // @codeCoverageIgnore
            }
        }
        if (!is_array($this->character->magics)) {
            $this->character->magics = []; // @codeCoverageIgnore
        }
        $magics = $this->character->magics;
        $magics['metamagics'] = $metaArray;
        $this->character->magics = $magics;
        return $this;
    }

    /**
     * @TODO Add cyberware modifications
     * @TODO Add cyberware grades
     */
    protected function parseAugmentations(
        SimpleXMLElement $aug
    ): Shadowrun5eConverter {
        $augmentationsArray = [];
        foreach ($aug->children() ?? [] as $item) {
            $name = (string)$item['name'];
            $name = explode(' (', $name);
            $name = $name[0];
            $id = $this->createIDFromName($name);
            $rating = (int)$item['rating'];
            if (0 !== $rating) {
                $id = sprintf('%s-%d', $id, $rating);
            }
            try {
                $augmentationsArray[] = [
                    'id' => (new Augmentation($id))->id,
                ];
            } catch (RuntimeException $ex) {
                $this->errors['augmentations'] ??= [];
                $this->errors['augmentations'][] = $ex->getMessage();
                continue;
            }
        }
        // Hero Lab stores bioware and cyberware separately, Commlink does not.
        // Merge the two together when loading the second type.
        $this->character->augmentations = array_merge(
            $this->character->augmentations ?? [],
            $augmentationsArray,
        );
        return $this;
    }

    /**
     * @TODO Handle accessories
     * @TODO Handle modifications
     */
    protected function parseWeapons(
        SimpleXMLElement $weapons
    ): Shadowrun5eConverter {
        $weaponsArray = [];
        foreach ($weapons->children() ?? [] as $rawWeapon) {
            $name = (string)$rawWeapon['name'];
            if (array_key_exists($name, $this->mapWeapons)) {
                if (null === $this->mapWeapons[$name]) {
                    continue;
                }
                $weaponsArray[] = [
                    'id' => (new Weapon($this->mapWeapons[$name]))->id,
                ];
                continue;
            }
            try {
                $weapon = Weapon::findByName($name);
            } catch (RuntimeException $ex) {
                $this->errors['weapons'] ??= [];
                $this->errors['weapons'][] = $ex->getMessage();
                continue;
            }
            $weaponsArray[] = [
                'id' => $weapon->id,
            ];
        }
        $this->character->weapons = $weaponsArray;
        return $this;
    }

    /**
     * @TODO Handle modifications
     */
    protected function parseArmor(
        SimpleXMLElement $armors
    ): Shadowrun5eConverter {
        $armorArray = [];
        foreach ($armors->children() ?? [] as $rawArmor) {
            $name = (string)$rawArmor['name'];
            if (array_key_exists($name, $this->mapArmor)) {
                if (null === $this->mapArmor[$name]) {
                    continue;
                }
                $armorArray[] = [
                    'id' => (new Armor($this->mapArmor[$name]))->id,
                ];
                continue;
            }
            try {
                $armor = Armor::findByName($name);
            } catch (RuntimeException $ex) {
                $this->errors['armor'] ??= [];
                $this->errors['armor'][] = $ex->getMessage();
                continue;
            }
            $armorArray[] = [
                'id' => $armor->id,
            ];
        }
        $this->character->armor = $armorArray;
        return $this;
    }

    /**
     * @TODO Handle modifications
     * @TODO Handle programs
     */
    protected function parseGear(SimpleXMLElement $gears): Shadowrun5eConverter
    {
        $gearArray = [];
        foreach ($gears->children() ?? [] as $rawGear) {
            $name = (string)$rawGear['name'];
            if (array_key_exists($name, $this->mapGear)) {
                if (null === $this->mapGear[$name]) {
                    // Item is explicitly not supported by Commlink.
                    continue;
                }
                try {
                    $gear = GearFactory::get($this->mapGear[$name]);
                } catch (RuntimeException $ex) {
                    $this->errors['gear'] ??= [];
                    $this->errors['gear'][] = $ex->getMessage();
                    continue;
                }
            } else {
                $rating = (int)$rawGear['rating'];
                $id = $this->createIDFromName($name);
                if (0 !== $rating) {
                    $id = sprintf('%s-%d', $id, $rating);
                }

                try {
                    $gear = GearFactory::get($id);
                } catch (RuntimeException) {
                    try {
                        $gear = Gear::findByName($name);
                    } catch (RuntimeException $ex) {
                        $this->errors['gear'] ??= [];
                        $this->errors['gear'][] = $ex->getMessage();
                        continue;
                    }
                }
            }
            $gear->quantity = (int)$rawGear['quantity'];
            $gearArray[] = [
                'id' => $gear->id,
                'quantity' => $gear->quantity,
            ];
        }
        $this->character->gear = $gearArray;
        return $this;
    }

    protected function parseIdentities(
        SimpleXMLElement $identities
    ): Shadowrun5eConverter {
        $i = 0;
        $identitiesArray = [];
        foreach ($identities->children() ?? [] as $rawIdentity) {
            $identity = [
                'id' => $i,
                'name' => (string)$rawIdentity['name'],
                'lifestyles' => [],
                'licenses' => [],
                'notes' => null,
                'sin' => null,
            ];
            foreach ($rawIdentity->lifestyle as $rawLifestyle) {
                $identity['lifestyle'][] = [
                    'name' => str_replace(
                        ' Lifestyle',
                        '',
                        (string)$rawLifestyle['name']
                    ),
                    'quantity' => (int)$rawLifestyle['months'],
                ];
            }
            foreach ($rawIdentity->license as $rawLicense) {
                if ('Fake License' === (string)$rawLicense['name']) {
                    $identity['licenses'][] = [
                        'rating' => (int)$rawLicense['rating'],
                        'license' => (string)$rawLicense['for'],
                    ];
                    continue;
                }
                $identity['sin'] = (int)$rawLicense['rating'];
            }
            $identitiesArray[] = $identity;
            $i++;
        }
        $this->character->identities = $identitiesArray;
        return $this;
    }

    protected function parseContacts(
        SimpleXMLElement $contacts
    ): Shadowrun5eConverter {
        $i = 0;
        $contactsArray = [];
        foreach ($contacts->children() ?? [] as $contact) {
            $contactsArray[] = [
                'archetype' => (string)$contact['type'],
                'connection' => (int)$contact['connection'],
                'id' => $i,
                'loyalty' => (int)$contact['loyalty'],
                'name' => (string)$contact['name'],
                'notes' => (string)$contact->description,
            ];
            $i++;
        }
        $this->character->contacts = $contactsArray;
        return $this;
    }

    /**
     * Given a priority from Herolab, return the matching Sum to Ten priority.
     */
    protected function priorityLetter(int $priority): string
    {
        return match ($priority) {
            1 => 'A',
            2 => 'B', // @codeCoverageIgnore
            3 => 'C',
            4 => 'D',
            5 => 'E',
            default => throw new RuntimeException('Invalid SumToTen priority'),
        };
    }

    protected function parsePriorities(): Shadowrun5eConverter
    {
        $priorities = [
            'metatype' => (string)$this->xml->race['name'],
        ];
        foreach ($this->xmlMeta->hero->container->pick as $value) {
            $priority = 0;
            foreach ($value->field as $field) {
                if ('priSmOrder' === (string)$field['id']) {
                    $priority = (int)$field['value'];
                }
            }
            if (0 === $priority) {
                continue;
            }
            switch ((string)$value['thing']) {
                case 'priAttr':
                    $priorities['attributePriority'] = $this->priorityLetter($priority);
                    break;
                case 'priMagic':
                    $priorities['magicPriority'] = $this->priorityLetter($priority);
                    break;
                case 'priMeta':
                    $priorities['metatypePriority'] = $this->priorityLetter($priority);
                    break;
                case 'priSkill':
                    $priorities['skillPriority'] = $this->priorityLetter($priority);
                    break;
                case 'priResourc':
                    $priorities['resourcePriority'] = $this->priorityLetter($priority);
                    break;
            }
        }
        $this->character->priorities = $priorities;
        return $this;
    }

    /**
     * Parse the 'source' information from the Hero Lab portfolio.
     *
     * The sources include the rulebooks loaded in Hero Lab, as well as the
     * priority system, gameplay level, life modules, etc.
     */
    protected function parseSources(): Shadowrun5eConverter
    {
        // Commented out rulesets are either not included in my copy of Herolab
        // (if the key is a question mark), or is a book I don't have included
        // in Commlink (value is a normal string).
        $map = [
            'Aether' => 'aetherology',
            'Assassin' => 'assassins-primer',
            //'?' => 'better-than-bad',
            'BloodyBus' => 'bloody-business',
            'BulletBand' => 'bullets-and-bandages',
            //'?' => 'book-of-the-lost',
            'ChromeFles' => 'chrome-flesh',
            //'?' => 'complete-trog',
            'CourtShad' => 'court-of-shadows',
            //'?' => 'coyotes',
            'CuttingAce' => 'cutting-aces',
            //'?' => 'dark-terrors',
            'DataTrails' => 'data-trails',
            //'?' => 'false-flag',
            //'?' => 'firing-line',
            //'?' => 'forbidden-arcana',
            'GunHeaven3' => 'gun-heaven-3',
            'HardTarget' => 'hard-targets',
            'HowlShadow' => 'howling-shadows',
            //'?' => 'kill-code',
            'Lockdown' => 'lockdown',
            //'?' => 'london-falling',
            //'?' => 'market-panic',
            //'NothingPer' => 'Nothing Personal',
            'Rigger5' => 'rigger-5',
            'RunGun' => 'run-and-gun',
            'RunFaster' => 'run-faster',
            //'SailAway' => 'Sail Away, Sweet Sister',
            //'?' => 'serrated-edge',
            'ShadSpells' => 'shadow-spells',
            'Butte' => 'shadows-in-focus-butte',
            //'?' => 'shadows-in-focus-casablance-rabat',
            //'?' => 'shadows-in-focus-cheyenne',
            //'Metropole' => 'Shadows in Focus: Metrópole',
            'SanFran' => 'shadows-in-focus-san-francisco-metroplex',
            'SiouxNat' => 'shadows-in-focus-sioux-nation',
            //'?' => 'splintered-state',
            //'?' => 'sprawl-wilds',
            'StolenSoul' => 'stolen-souls',
            'StreetGrim' => 'street-grimoire',
            //'?' => 'street-lethal',
            //'?' => 'streetpedia',
            //'?' => 'ten-terrorists',
            //'?' => 'toxic-alleys',
            //'?' => 'unoriginal-sin',
            'VladGaunt' => 'vladivostok-guantlet',
        ];

        $priorities = $this->character->priorities ?? [];
        $books = ['core'];

        foreach ($this->xmlMeta->hero->source as $value) {
            $source = (string)$value['source'];
            $enabled = '1' === (string)$value['count'];
            if (isset($map[$source]) && $enabled) {
                $books[] = $map[$source];
                continue;
            }
            if ('SumToTen' === $source && $enabled) {
                // Sum-to-ten chargen requires Run Faster.
                $books[] = 'run-faster';
                $priorities['system'] = 'sum-to-ten';
                continue;
            }
            if ('AltNormal' === $source && $enabled) {
                $priorities['gameplay'] = 'established';
                continue;
            }
            if ('AltPrime' === $source && $enabled) {
                $priorities['gameplay'] = 'prime';
                continue;
            }
            if ('AltStreet' === $source && $enabled) {
                $priorities['gameplay'] = 'street';
                continue;
            }
            if ('LifeModule' === $source && $enabled) {
                $priorities['system'] = 'life-module';
                $books[] = 'run-faster';
                continue;
            }
            if ('PointBuy' === $source && $enabled) {
                $priorities['system'] = 'point-buy';
                $books[] = 'run-faster';
            }
        }
        sort($books);
        $books = array_unique($books);
        $priorities['rulebooks'] = implode(',', array_unique($books));
        $this->character->priorities = $priorities;
        return $this;
    }

    protected function parseJournals(
        SimpleXMLElement $journals
    ): Shadowrun5eConverter {
        return $this;
    }

    /**
     * @return array<string, string|array<mixed, mixed>>
     */
    protected function parseVehicleStatBlock(string $stats, string $name): array
    {
        $stats = explode(PHP_EOL, $stats);
        $modifications = [];
        $weapons = [];
        $gear = [];
        $vehicle = null;

        while ($line = current($stats)) {
            if (str_starts_with($line, 'CHASSIS: ')) {
                $id = explode(': ', $line)[1];
                if (isset($this->mapVehicles[$id])) {
                    $vehicle = new Vehicle(['id' => $this->mapVehicles[$id]]);
                    next($stats);
                    continue;
                }
                $id = $this->createIDFromName($id);
                try {
                    $vehicle = new Vehicle(['id' => $id]);
                    next($stats);
                    continue;
                } catch (RuntimeException) {
                    // Ignore and try finding by name.
                }

                next($stats);
                continue;
            }
            if (str_starts_with($line, 'Vehicle Mods:')) {
                $line = next($stats);
                while (str_starts_with((string)$line, ' ')) {
                    $line = trim((string)$line);
                    if (isset($this->mapVehicleModifications[$line])) {
                        // @phpstan-ignore identical.alwaysFalse
                        if (null === $this->mapVehicleModifications[$line]) {
                            $line = next($stats);
                            continue;
                        }
                        $modifications[] = $this->mapVehicleModifications[$line];
                        $line = next($stats);
                        continue;
                    }
                    if (str_contains($line, '(')) {
                        [$id, $rating] = explode(' (', $line);
                        $rating = (int)$rating;
                        $id = $this->createIDFromName($id);
                        if (0 === $rating) {
                            try {
                                $mod = new VehicleModification($id);
                                $modifications[] = ['id' => $mod->id];
                                $line = next($stats);
                                continue;
                            } catch (RuntimeException) {
                            }
                        }
                        try {
                            $id = sprintf('%s-%d', $id, $rating);
                            $mod = new VehicleModification($id);
                            $modifications[] = ['id' => $mod->id];
                            $line = next($stats);
                            continue;
                        } catch (RuntimeException) {
                        }
                    }
                    try {
                        $mod = new VehicleModification($this->createIDFromName($line));
                        $modifications[] = ['id' => $mod->id];
                        $line = next($stats);
                        continue;
                    } catch (RuntimeException $ex) {
                        $this->errors['vehicles'] ??= [];
                        $this->errors['vehicles'][] = $ex->getMessage();
                    }
                    $line = next($stats);
                }
                continue;
            }
            if (str_starts_with($line, 'Gear:')) {
                $line = next($stats);
                while (str_starts_with((string)$line, ' ')) {
                    $line = trim((string)$line);
                    if (array_key_exists($line, $this->mapGear)) {
                        if (null === $this->mapGear[$line]) {
                            // Item is explicitly not supported by Commlink.
                            $line = next($stats);
                            continue;
                        }
                        $gear[] = ['id' => $this->mapGear[$line]];
                        $line = next($stats);
                        continue;
                    }
                    if (str_contains($line, ' (')) {
                        [$line, $rating] = explode(' (', $line);
                        $id = sprintf(
                            '%s-%d',
                            $this->createIDFromName($line),
                            (int)$rating
                        );
                        GearFactory::get($id);
                        $gear[] = ['id' => $id];
                        $line = next($stats);
                        continue;
                    }
                    if (str_contains($line, ': ')) {
                        [$line, $subname] = explode(': ', $line);
                        $item = Gear::findByName($line);
                        $gear[] = ['id' => $item->id, 'subname' => $subname];
                        $line = next($stats);
                        continue;
                    }

                    try {
                        $item = GearFactory::get($this->createIDFromName($line));
                        $gear = ['id' => $item->id];
                    } catch (RuntimeException) {
                    }
                    $line = (string)next($stats);
                }
            }
            if (str_starts_with((string)$line, 'Weapons:')) {
                $line = next($stats);
                while (str_starts_with((string)$line, ' ')) {
                    $line = trim((string)$line);
                    $weaponMods = [];
                    $ammo = [];
                    [$weapon, $mods] = explode(' [', $line);
                    if (array_key_exists($weapon, $this->mapWeapons)) {
                        if (null === $this->mapWeapons[$weapon]) {
                            // Weapon is explicitly not supported.
                            $line = next($stats);
                            continue;
                        }
                        $weapon = new Weapon($this->mapWeapons[$weapon]);
                    } else {
                        try {
                            $weapon = Weapon::findByName($weapon);
                        } catch (RuntimeException $ex) {
                            $this->errors['vehicles'] ??= [];
                            $this->errors['vehicles'][] = $ex->getMessage();
                            $line = next($stats);
                            continue;
                        }
                    }
                    [, $mods] = explode(']', $mods);
                    $mods = str_replace('w/ ', '', trim($mods));
                    $mods = explode(', ', $mods);
                    for ($i = 0, $c = count($mods); $i < $c; $i++) {
                        if ('Smartgun System' === $mods[$i]) {
                            // Exploding on a comma doesn't work with data
                            // including commas...
                            $id = sprintf('smartlink-%s', strtolower($mods[++$i]));
                            $weaponMods[] = (new WeaponModification($id))->id;
                            continue;
                        }
                        if (str_contains($mods[$i], 'x)')) {
                            [$quantity, $type] = explode('x) ', $mods[$i]);
                            $ammo[] = [
                                'id' => $this->createIDFromName($type),
                                'quantity' => (int)trim($quantity, '('),
                            ];
                        }
                    }
                    $weapons[] = [
                        'id' => $weapon->id,
                        'modifications' => $weaponMods,
                        'ammo' => $ammo,
                    ];
                    $line = (string)next($stats);
                }
                continue;
            }
            next($stats);
        }
        if (null === $vehicle) {
            throw new RuntimeException(sprintf(
                'Could not parse stats for "%s"',
                $name,
            ));
        }
        if ($name === $vehicle->name) {
            return [
                'id' => $vehicle->id,
                'gear' => $gear,
                'modifications' => $modifications,
                'weapons' => $weapons,
            ];
        }
        return [
            'id' => $vehicle->id,
            'gear' => $gear,
            'modifications' => $modifications,
            'subname' => $name,
            'weapons' => $weapons,
        ];
    }

    protected function parseVehicles(): Shadowrun5eConverter
    {
        $vehiclesArray = [];
        foreach ($this->xmlMeta->hero->container->pick as $rawVehicle) {
            if ('vehVehicle' !== (string)$rawVehicle['source']) {
                continue;
            }
            $name = (string)$rawVehicle->minion['heroname'];
            $stats = false;
            foreach ($this->minions->children() as $minion) {
                if ((string)$minion['name'] !== $name) {
                    continue;
                }
                foreach ($minion->statblocks->children() as $stat) {
                    if ('text' !== (string)$stat['format']) {
                        continue;
                    }
                    $file = sprintf(
                        '%s%s%s%s%s',
                        $this->directory,
                        DIRECTORY_SEPARATOR,
                        (string)$stat['folder'],
                        DIRECTORY_SEPARATOR,
                        (string)$stat['filename'],
                    );
                    $stats = file_get_contents($file);
                }
            }
            if (false === $stats) {
                $this->errors['vehicles'] ??= [];
                $this->errors['vehicles'][] = sprintf(
                    'Vehicle "%s" is missing stats',
                    $name,
                );
                continue;
            }

            try {
                $vehiclesArray[] = $this->parseVehicleStatBlock($stats, $name);
            } catch (RuntimeException $ex) {
                $this->errors['vehicles'] ??= [];
                $this->errors['vehicles'][] = $ex->getMessage();
            }
        }
        $this->character->vehicles = $vehiclesArray;
        return $this;
    }

    /**
     * Hero Lab includes skills from skill groups in the character's skills
     * list. Commlink doesn't.
     */
    protected function cleanSkillsAlsoInSkillGroups(): Shadowrun5eConverter
    {
        $skills = $this->character->skills;
        foreach ($this->character->skillGroups ?? [] as $group => $level) {
            $group = new SkillGroup($group, $level ?? 1);
            $groupSkills = [];
            foreach ($group->skills as $skill) {
                $groupSkills[] = $skill->id;
            }
            foreach ($this->character->skills ?? [] as $index => $skill) {
                if (in_array($skill['id'], $groupSkills, true)) {
                    unset($skills[$index]);
                }
            }
        }
        $this->character->skills = $skills;
        return $this;
    }

    /**
     * Convert a loaded Hero Lab portfolio to a Commlink character.
     */
    #[Override]
    public function convert(): PartialCharacter
    {
        $this->character->handle = (string)$this->xml['name'];
        $this->character->karmaCurrent = (int)$this->xml->karma['left'];
        $this->character->karma = (int)$this->xml->karma['total'];
        $this->character->nuyen = (int)$this->xml->cash['total'];
        $this->character->gender
            = strtolower((string)$this->xml->personal['gender']);
        $this->character->background = [
            'age' => (int)$this->xml->personal['age'],
            'hair' => (string)$this->xml->personal['hair'],
            'eyes' => (string)$this->xml->personal['eyes'],
            'skin' => (string)$this->xml->personal['skin'],
        ];

        $this->parsePriorities()
            ->parseSources()
            ->parseAttributes($this->xml->attributes->attribute)
            ->parseQualities($this->xml->qualities->positive)
            ->parseQualities($this->xml->qualities->negative)
            ->parseSkillGroups($this->xml->skills->groups)
            ->parseActiveSkills($this->xml->skills->active)
            ->parseKnowledgeSkills($this->xml->skills->knowledge, false)
            ->parseKnowledgeSkills($this->xml->skills->language, true)
            ->parseSpells($this->xml->magic->spells)
            ->parsePowers($this->xml->magic->adeptpowers)
            ->parseMetamagics($this->xml->magic->metamagics)
            ->parseAugmentations($this->xml->gear->augmentations->cyberware)
            ->parseAugmentations($this->xml->gear->augmentations->bioware)
            ->parseWeapons($this->xml->gear->weapons)
            ->parseArmor($this->xml->gear->armor)
            ->parseGear($this->xml->gear->equipment)
            ->parseIdentities($this->xml->identities)
            ->parseContacts($this->xml->contacts)
            ->parseJournals($this->xml->journals->journal)
            ->parseVehicles()
            ->cleanSkillsAlsoInSkillGroups();

        return $this->character;
    }

    /**
     * @return array<string, array<int, string>>
     */
    #[Override]
    public function getErrors(): array
    {
        return $this->errors;
    }
}
