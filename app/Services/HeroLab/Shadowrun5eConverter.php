<?php

declare(strict_types=1);

namespace App\Services\HeroLab;

use App\Models\Shadowrun5E\ActiveSkill;
use App\Models\Shadowrun5E\AdeptPower;
use App\Models\Shadowrun5E\Armor;
use App\Models\Shadowrun5E\Augmentation;
use App\Models\Shadowrun5E\Character;
use App\Models\Shadowrun5E\Gear;
use App\Models\Shadowrun5E\GearFactory;
use App\Models\Shadowrun5E\Quality;
use App\Models\Shadowrun5E\SkillGroup;
use App\Models\Shadowrun5E\Weapon;
use App\Services\ConverterInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

/**
 * Importer class for Hero Lab Shadowrun 5E profiles.
 */
class Shadowrun5eConverter implements ConverterInterface
{
    /**
     * @var Character Character being converted.
     */
    protected Character $character;

    /**
     * @var string Directory the portfolio was extracted to.
     */
    protected string $directory;

    /**
     * Errors encountered and not handled.
     * @var array<int, string>
     */
    protected array $errors = [];

    /**
     * Map of Hero Lab gear names to Commlink IDs.
     * @var array<string, ?string>
     */
    protected array $mapGear = [
        'Certified Credstick, Silver' => 'credstick-silver',
    ];

    /**
     * Map of Hero Lab quality names to Commlink IDs or null for those ignored.
     * @var array<string, ?string>
     */
    protected array $mapQualities = [
        'Adept' => null,
        'Insomnia (Half-Speed Recovery) (7dicepool vs. 4)' => 'insomnia-1',
    ];

    /**
     * Map of Hero Lab weapons to Commlink IDs.
     * @var array<string, ?string>
     */
    protected array $mapWeapons = [
        'Unarmed Strike' => null,
    ];

    /**
     * @var SimpleXMLElement
     */
    protected SimpleXMLElement $xml;

    /**
     * Constructor.
     * @param string $filename
     * @throws RuntimeException
     */
    public function __construct(string $filename)
    {
        $this->createTemporaryDirectory();
        $this->extractArchive($filename);
        $this->parseFiles();
    }

    /**
     * Clean up any temporary files left over.
     */
    public function __destruct()
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
     * @param string $filename
     * @throws RuntimeException
     */
    protected function extractArchive(string $filename): void
    {
        $zip = new ZipArchive();
        $res = $zip->open($filename, ZipArchive::RDONLY);
        if (true !== $res) {
            switch ($res) {
                case ZipArchive::ER_NOZIP:
                    throw new RuntimeException('Portfolio is not valid');
                case ZipArchive::ER_NOENT:
                    throw new RuntimeException('Portfolio not found');
                default:
                    throw new RuntimeException(sprintf(
                        'Opening portfolio failed with unknown code: %d',
                        $res
                    ));
            }
        }

        // @codeCoverageIgnoreStart
        if (false === $zip->extractTo($this->directory)) {
            throw new RuntimeException(
                'Unable to extract Portfolio to temporary directory'
            );
        }
        // @codeCoverageIgnoreEnd

        $zip->close();
    }

    /**
     * Parse the XML files from the portfolio.
     * @throws RuntimeException
     */
    protected function parseFiles(): void
    {
        $files = glob(sprintf(
            '%s%sstatblocks_xml%s*',
            $this->directory,
            \DIRECTORY_SEPARATOR,
            \DIRECTORY_SEPARATOR
        ));
        $xml = false;
        // @phpstan-ignore-next-line
        foreach ($files as $file) {
            if (false === strpos($file, '.xml')) {
                continue;
            }
            $xml = simplexml_load_file($file);
            break;
        }
        if (false === $xml) {
            throw new RuntimeException('Failed to load Portfolio stats');
        }
        $this->xml = $xml->public->character;
    }

    /**
     * Convert a name to something that might be a valid Commlink ID.
     * @param string $name
     * @return string
     */
    public function createIDFromName(string $name): string
    {
        return strtolower(str_replace(
            [' ', ':', ','],
            ['-', '', ''],
            $name
        ));
    }

    /**
     * Parse the character's attributes out from the XML.
     * @param SimpleXMLElement $attributes
     * @return Shadowrun5eConverter
     */
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
            // @phpstan-ignore-next-line
            $this->character->$attribute = (int)$rawAttribute['base'];
        }
        return $this;
    }

    /**
     * Parse out the character's qualities.
     * @param SimpleXMLElement $qualities
     * @return Shadowrun5eConverter
     */
    protected function parseQualities(
        SimpleXMLElement $qualities
    ): Shadowrun5eConverter {
        $qualitiesArray = $this->character->qualities ?? [];
        foreach ($qualities as $rawQuality) {
            $name = (string)$rawQuality['name'];
            if (array_key_exists($name, $this->mapQualities)) {
                if (null === $this->mapQualities[$name]) {
                    continue;
                }
                $quality = new Quality($this->mapQualities[$name]);
                $qualitiesArray[] = [
                    'id' => $quality->id,
                ];
                continue;
            }

            try {
                $quality = Quality::findByName($name);
                $qualitiesArray[] = [
                    'id' => $quality->id,
                ];
                continue;
            } catch (RuntimeException $ex) {
                // Ignore and try other ways of finding the Quality.
            }

            if (false !== strpos($name, ':')) {
                [$shortName, $extra] = explode(':', $name);
                try {
                    $found = Quality::findByName($shortName);
                    $qualitiesArray[] = [
                        'id' => $found->id,
                        'severity' => $extra,
                    ];
                    continue;
                } catch (RuntimeException $ex) {
                    // Fall through to adding an error.
                }
            }
            $this->errors[] = sprintf('Quality "%s" was not found.', $name);
        }
        $this->character->qualities = $qualitiesArray;
        return $this;
    }

    /**
     * Parse out the character's skill groups.
     * @param SimpleXMLElement $groups
     * @return Shadowrun5eConverter
     */
    protected function parseSkillGroups(
        SimpleXMLElement $groups
    ): Shadowrun5eConverter {
        $skillGroups = [];
        foreach ($groups as $group) {
            $id = (string)$group['name'];
            $id = str_replace(' Group', '', $id);
            $id = strtolower($id);
            $level = (int)$group['base'];
            $group = new SkillGroup($id, $level);
            $skillGroups[$group->id] = $group->level;
        }
        $this->character->skillGroups = $skillGroups;
        return $this;
    }

    /**
     * Parse out the character's active skills.
     * @param SimpleXMLElement $skills
     * @return Shadowrun5eConverter
     */
    protected function parseActiveSkills(
        SimpleXMLElement $skills
    ): Shadowrun5eConverter {
        $skillsArray = [];
        foreach ($skills as $skill) {
            $id = $this->createIDFromName((string)$skill['name']);
            $level = (int)$skill['base'];
            $skillObject = new ActiveSkill($id, $level);
            $skillsArray[] = [
                'id' => $skillObject->id,
                'level' => $skill->level,
            ];
        }
        $this->character->skills = $skillsArray;
        return $this;
    }

    /**
     * Parse out the character's knowledge skills (regular or language).
     * @param SimpleXMLElement $skills
     * @param bool $isLanguage
     * @return Shadowrun5eConverter
     */
    protected function parseKnowledgeSkills(
        SimpleXMLElement $skills,
        bool $isLanguage
    ): Shadowrun5eConverter {
        $category = $isLanguage ? 'language' : 'street';
        $skillsArray = $this->character->knowledgeSkills ?? [];
        foreach ($skills as $skill) {
            $name = (string)$skill['name'];
            $rating = (int)$skill['base'];
            if ($skill->specialization) {
                $specializations = [];
                foreach ($skill->specialization as $spec) {
                    $specializations[]
                        = str_replace(' +2', '', (string)$spec['bonustext']);
                }
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
     * Parse out the character's spells.
     * @param SimpleXMLElement $spells
     * @return Shadowrun5eConverter
     * @TODO Implement
     */
    protected function parseSpells(
        SimpleXMLElement $spells
    ): Shadowrun5eConverter {
        return $this;
    }

    /**
     * Parse out the character's powers.
     * @param SimpleXMLElement $powers
     * @return Shadowrun5eConverter
     */
    protected function parsePowers(
        SimpleXMLElement $powers
    ): Shadowrun5eConverter {
        if (0 === count($powers)) {
            return $this;
        }
        $powersArray = [];
        foreach ($powers as $power) {
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
            $this->character->magics = [];
        }
        $magics = $this->character->magics;
        $magics['powers'] = $powersArray;
        $this->character->magics = $magics;
        return $this;
    }

    /**
     * Parse out the character's metamagics.
     * @param SimpleXMLElement $meta
     * @return Shadowrun5eConverter
     * @TODO Implement
     */
    protected function parseMetamagics(
        SimpleXMLElement $meta
    ): Shadowrun5eConverter {
        return $this;
    }

    /**
     * Parse out the character's augmentations.
     * @param SimpleXMLElement $aug
     * @return Shadowrun5eConverter
     * @TODO Add cyberware modifications
     * @TODO Add cyberware grades
     */
    protected function parseAugmentations(
        SimpleXMLElement $aug
    ): Shadowrun5eConverter {
        $augmentationsArray = [];
        foreach ($aug as $item) {
            $name = (string)$item['name'];
            $name = explode(' (', $name);
            $name = $name[0];
            $id = $this->createIDFromName($name);
            $rating = (int)$item['rating'];
            if (0 !== $rating) {
                $id = sprintf('%s-%d', $id, $rating);
            }
            $augmentationsArray[] = [
                'id' => (new Augmentation($id))->id,
            ];
        }
        $this->character->augmentations = $augmentationsArray;
        return $this;
    }

    /**
     * Parse the character's weapons.
     * @param SimpleXMLElement $weapons
     * @return Shadowrun5eConverter
     * @TODO Handle accessories
     * @TODO Handle modifications
     */
    protected function parseWeapons(
        SimpleXMLElement $weapons
    ): Shadowrun5eConverter {
        $weaponsArray = [];
        foreach ($weapons as $rawWeapon) {
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
            } catch (\RuntimeException $ex) {
                $this->errors[] = $ex->getMessage();
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
     * Parse the character's armor.
     * @param SimpleXMLElement $armors
     * @return Shadowrun5eConverter
     * @TODO Handle modifications
     */
    protected function parseArmor(
        SimpleXMLElement $armors
    ): Shadowrun5eConverter {
        $armorArray = [];
        foreach ($armors as $rawArmor) {
            $name = (string)$rawArmor['name'];
            try {
                $armor = Armor::findByName($name);
            } catch (\RuntimeException $ex) {
                $this->errors[] = $ex->getMessage();
                continue;
            }
            $armorArray[] = [
                'id' => $armor,
            ];
        }
        $this->character->armor = $armorArray;
        return $this;
    }

    /**
     * Parse the character's gear.
     * @param SimpleXMLElement $gears
     * @return Shadowrun5eConverter
     * @TODO Handle modifications
     * @TODO Handle programs
     */
    protected function parseGear(SimpleXMLElement $gears): Shadowrun5eConverter
    {
        $gearArray = [];
        foreach ($gears as $rawGear) {
            $name = (string)$rawGear['name'];
            if (array_key_exists($name, $this->mapGear)) {
                if (null === $this->mapGear[$name]) {
                    // Item is explicitly not supported by Commlink.
                    continue;
                }
                $gear = GearFactory::get($this->mapGear[$name]);
            } else {
                $rating = (int)$rawGear['rating'];
                $id = $this->createIDFromName($name);
                if (0 !== $rating) {
                    $id = sprintf('%s-%d', $id, $rating);
                }

                try {
                    $gear = GearFactory::get($id);
                } catch (\RuntimeException $ex) {
                    try {
                        $gear = Gear::findByName($name);
                    } catch (\RuntimeException $ex) {
                        $this->errors[] = $ex->getMessage();
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

    /**
     * Parse the character's identities.
     * @param SimpleXMLElement $identities
     * @return Shadowrun5eConverter
     */
    protected function parseIdentities(
        SimpleXMLElement $identities
    ): Shadowrun5eConverter {
        $i = 0;
        $identitiesArray = [];
        foreach ($identities as $rawIdentity) {
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

    /**
     * Parse the character's contacts.
     * @param SimpleXMLElement $contacts
     * @return Shadowrun5eConverter
     */
    protected function parseContacts(
        SimpleXMLElement $contacts
    ): Shadowrun5eConverter {
        $i = 0;
        $contactsArray = [];
        foreach ($contacts as $contact) {
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
     * Convert a loaded Hero Lab portfolio to a Commlink character.
     * @return Character
     */
    public function convert(): Character
    {
        $this->character = new Character();

        $this->character->handle = (string)$this->xml['name'];
        $this->character->priorities = [
            'metatype' => (string)$this->xml->race['name'],
        ];
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

        $this->parseAttributes($this->xml->attributes->attribute)
            ->parseQualities($this->xml->qualities->positive->quality)
            ->parseQualities($this->xml->qualities->negative->quality)
            ->parseSkillGroups($this->xml->skills->groups->skill)
            ->parseActiveSkills($this->xml->skills->active->skill)
            ->parseKnowledgeSkills($this->xml->skills->knowledge->skill, false)
            ->parseKnowledgeSkills($this->xml->skills->language->skill, true)
            ->parseSpells($this->xml->magic->spells->spell)
            ->parsePowers($this->xml->magic->adeptpowers->adeptpower)
            ->parseMetamagics($this->xml->magic->metamagics->metamagic)
            ->parseAugmentations($this->xml->gear->augmentations->cyberware->item)
            ->parseAugmentations($this->xml->gear->augmentations->bioware->item)
            ->parseWeapons($this->xml->gear->weapons->item)
            ->parseArmor($this->xml->gear->armor->item)
            ->parseGear($this->xml->gear->equipment->item)
            ->parseIdentities($this->xml->identities->identity)
            ->parseContacts($this->xml->contacts->contact);

        return $this->character;
    }

    /**
     * Return the list of errors produced during conversion.
     * @return array<int, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
