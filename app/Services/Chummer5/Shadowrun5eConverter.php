<?php

declare(strict_types=1);

namespace App\Services\Chummer5;

use App\Models\Shadowrun5e\ActiveSkill;
use App\Models\Shadowrun5e\Armor;
use App\Models\Shadowrun5e\ArmorModification;
use App\Models\Shadowrun5e\Gear;
use App\Models\Shadowrun5e\GearFactory;
use App\Models\Shadowrun5e\Lifestyle;
use App\Models\Shadowrun5e\PartialCharacter as Character;
use App\Models\Shadowrun5e\Quality;
use App\Models\Shadowrun5e\Spell;
use App\Models\Shadowrun5e\Tradition;
use App\Models\Shadowrun5e\Weapon;
use App\Services\ConverterInterface;
use RuntimeException;
use SimpleXMLElement;

/**
 * Converter class to convert a Chummer 5 file to a Commlink Character.
 */
class Shadowrun5eConverter implements ConverterInterface
{
    /**
     * Character after conversion.
     * @var Character
     */
    protected Character $character;

    /**
     * Errors encountered and not handled.
     * @var array<int, string>
     */
    protected array $errors = [];

    protected bool $isAdept;
    protected bool $isMagician;
    protected bool $isTechnomancer;

    /**
     * Map of Chummer gear names to Commlink IDs or null for ignore items.
     * @var array<string, ?string>
     */
    protected array $mapGear = [
        'Certified Credstick, Gold' => 'credstick-gold',
        'Certified Credstick, Silver' => 'credstick-silver',
        'Certified Credstick, Standard' => 'credstick-standard',
        // Fake SINs are handled as identities, not gear.
        'Fake SIN' => null,
        'Meta Link' => 'commlink-metalink',
    ];

    /**
     * Map of Chummer quality names to Commlink IDs or null for those ignored.
     * @var array<string, ?string>
     */
    protected array $mapQualities = [
        'Magician' => null,
    ];

    /**
     * XML from the uploaded file.
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
        if (!file_exists($filename)) {
            throw new RuntimeException(sprintf(
                '"%s" does not exist',
                $filename
            ));
        }

        try {
            // @phpstan-ignore-next-line
            $this->xml = simplexml_load_file($filename);
        } catch (\ErrorException) {
            throw new RuntimeException('Could not parse XML in Chummer file');
        }

        $this->character = new Character();
    }

    /**
     * Return any conversion errors.
     * @return array<int, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
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

    protected function loadArmor(): Shadowrun5eConverter
    {
        $armors = [];
        foreach ($this->xml->armors->armor as $rawArmor) {
            try {
                $armor = Armor::findByName((string)$rawArmor->name);
            } catch (RuntimeException $ex) {
                $this->errors[] = $ex->getMessage();
                continue;
            }

            $mods = [];
            foreach ($rawArmor->armormods->armormod as $rawMod) {
                try {
                    $mod = new ArmorModification(
                        $this->createIDFromName((string)$rawMod->name)
                    );
                    $mods[] = $mod->id;
                } catch (RuntimeException $ex) {
                    $this->errors[] = sprintf(
                        'Could not find armor modification "%s" for "%s"',
                        (string)$rawMod->name,
                        $armor->name
                    );
                }
            }
            $armors[] = [
                'id' => $armor->id,
                'modifications' => $mods,
            ];
        }
        $this->character->armor = $armors;
        return $this;
    }

    protected function loadAttributes(): Shadowrun5eConverter
    {
        $attributes = [
            'AGI' => 'agility',
            'BOD' => 'body',
            'CHA' => 'charisma',
            'EDG' => 'edge',
            'INT' => 'intuition',
            'LOG' => 'logic',
            'MAG' => 'magic',
            'REA' => 'reaction',
            'RES' => 'resonance',
            'STR' => 'strength',
            'WIL' => 'willpower',
        ];
        foreach ($this->xml->attributes->attribute as $attribute) {
            if (!array_key_exists((string)$attribute->name, $attributes)) {
                continue;
            }

            $attributeName = $attributes[(string)$attribute->name];

            if (!$this->isAdept && !$this->isMagician && 'magic' === $attributeName) {
                continue;
            }
            if (!$this->isTechnomancer && 'resonance' === $attributeName) {
                continue;
            }

            // @phpstan-ignore-next-line
            $this->character->$attributeName =
                (int)$attribute->metatypemin + (int)$attribute->base
                + (int)$attribute->karma;
        }

        return $this;
    }

    protected function loadAugmentations(): Shadowrun5eConverter
    {
        $augmentations = [];
        $this->character->augmentations = $augmentations;
        return $this;
    }

    protected function loadContacts(): Shadowrun5eConverter
    {
        $contacts = [];
        $i = 0;
        foreach ($this->xml->contacts->contact as $contact) {
            $contacts[] = [
                'archetype' => (string)$contact->role,
                'connection' => (int)$contact->connection,
                'id' => $i,
                'loyalty' => (int)$contact->loyalty,
                'name' => (string)$contact->name,
            ];
            $i++;
        }
        $this->character->contacts = $contacts;
        return $this;
    }

    protected function loadGear(): Shadowrun5eConverter
    {
        $gear = [];
        foreach ($this->xml->gears->gear as $rawItem) {
            $name = (string)$rawItem->name;

            if (array_key_exists($name, $this->mapGear)) {
                if (null === $this->mapGear[$name]) {
                    // Item is explicitly not supported by Commlink.
                    continue;
                }
                try {
                    $item = GearFactory::get($this->mapGear[$name]);
                    $gear[] = [
                        'id' => $item->id,
                        'quantity' => (int)$rawItem->qty,
                    ];
                    continue;
                } catch (RuntimeException) {
                    // Ignore and try other ways of loading the item.
                }
            }

            try {
                $id = $this->createIDFromName($name);
                $rating = (int)$rawItem->rating;
                if (0 !== $rating) {
                    $id = sprintf('%s-%d', $id, $rating);
                }
                $item = GearFactory::get($id);
                $gear[] = [
                    'id' => $item->id,
                    'quantity' => (int)$rawItem->qty,
                ];
                continue;
            } catch (RuntimeException $ex) {
                // Ignore and try to find another way.
            }

            try {
                $item = Gear::findByName($name);
                $gear[] = [
                    'id' => $item->id,
                    'quantity' => (int)$rawItem->qty,
                ];
            } catch (RuntimeException $ex) {
                $this->errors[] = $ex->getMessage();
            }
        }
        $this->character->gear = $gear;
        return $this;
    }

    public function loadIdentities(): Shadowrun5eConverter
    {
        $identities = [];
        $i = 0;
        $highest = null;
        // First, find out whether the character has a fake SIN.
        foreach ($this->xml->gears->gear as $item) {
            if ('Fake SIN' !== (string)$item->name) {
                continue;
            }
            $identity = [
                'id' => $i,
                'licenses' => [],
                'lifestyles' => [],
                'name' => (string)$item->extra,
                'notes' => null,
                'sin' => (int)$item->rating,
            ];
            foreach ($item->children->gear as $license) {
                if ('Fake License' !== (string)$license->name) {
                    continue;
                }
                $identity['licenses'][] = [
                    'rating' => (int)$license->rating,
                    'license' => (string)$license->extra,
                ];
            }

            $identities[] = $identity;
            $i++;
        }
        if (0 === count($identities)) {
            // The character has no fake SINs, thus no identities to attach
            // lifestyles to.
            return $this;
        }

        // Lifestyles in Chummer aren't linked to an identity, but they are in
        // Commlink. We'll just add them to the first identity.
        foreach ($this->xml->lifestyles->lifestyle as $rawLifestyle) {
            try {
                $lifestyle = new Lifestyle(
                    $this->createIDFromName((string)$rawLifestyle->baselifestyle)
                );
            } catch (RuntimeException $ex) {
                $this->errors[] = $ex->getMessage();
                continue;
            }
            $identities[0]['lifestyles'][] = [
                'name' => $lifestyle->name,
                'quantity' => (int)$rawLifestyle->months,
            ];
        }

        $this->character->identities = $identities;
        return $this;
    }

    public function loadKnowledgeSkills(): Shadowrun5eConverter
    {
        $skills = [];
        foreach ($this->xml->newskills->knoskills->skill as $skill) {
            $name = (string)$skill->name;
            $level = (int)$skill->base;
            $category = strtolower((string)$skill->type);

            if ('interest' === $category) {
                // Chummer and Commlink disagree on what to call the category.
                $category = 'interests';
            } elseif (0 === $level && 'language' === $category) {
                // Chummer adds language with no ranks as native.
                $level = 'N';
            }
            $spec = null;
            if (isset($skill->specs)) {
                // Skill has at least one specialization.
                $specs = [];
                foreach ($skill->specs->spec as $value) {
                    $specs[] = (string)$value->name;
                }
                $spec = implode(',', $specs);
            }
            $skills[] = [
                'category' => $category,
                'name' => $name,
                'level' => $level,
                'specialization' => $spec,
            ];
        }
        $this->character->knowledgeSkills = $skills;
        return $this;
    }

    protected function loadMentorSpirit(): Shadowrun5eConverter
    {
        return $this;
    }

    protected function loadMetadata(): Shadowrun5eConverter
    {
        $this->character->background = [
            'age' => (string)$this->xml->age,
            'background' => (string)$this->xml->background,
            'concept' => (string)$this->xml->concept,
            'description' => (string)$this->xml->description,
            'eyes' => (string)$this->xml->eyes,
            'hair' => (string)$this->xml->hair,
            'height' => (string)$this->xml->height,
            'skin' => (string)$this->xml->skin,
            'weight' => (string)$this->xml->weight,
        ];
        $this->character->handle = (string)$this->xml->alias;
        $this->character->karma = (int)$this->xml->karma;
        $this->character->karmaCurrent = (int)$this->xml->karma;
        $this->character->metatype = strtolower((string)$this->xml->metatype);
        $this->character->nuyen = (int)$this->xml->nuyen;
        $this->character->realName = (string)$this->xml->name;
        return $this;
    }

    protected function loadPriorities(): Shadowrun5eConverter
    {
        $this->character->priorities = [
            'metatype' => strtolower((string)$this->xml->metatype),
            'metatypePriority' => substr((string)$this->xml->prioritymetatype, 0, 1),
            'attributePriority' => substr((string)$this->xml->priorityattributes, 0, 1),
            'magicPriority' => substr((string)$this->xml->priorityspecial, 0, 1),
            'skillPriority' => substr((string)$this->xml->priorityskills, 0, 1),
            'resourcePriority' => substr((string)$this->xml->priorityresources, 0, 1),
            'magic' => (string)$this->xml->prioritytalent,
        ];
        if ('E' !== $this->character->priorities['magicPriority']) {
            $this->character->magics = [];
        }
        return $this;
    }

    protected function loadQualities(): Shadowrun5eConverter
    {
        $qualities = [];
        foreach ($this->xml->qualities->quality as $rawQuality) {
            $name = (string)$rawQuality->name;
            if (array_key_exists($name, $this->mapQualities)) {
                if (null === $this->mapQualities[$name]) {
                    // Ignore the quality.
                    continue;
                }
                // Chummer calls the quality something weird, we've got a map
                // to the Commlink ID.
                $qualities = $this->mapQualities[$name];
                continue;
            }
            try {
                $quality = Quality::findByName($name);
            } catch (RuntimeException $ex) {
                $this->errors[] = $ex->getMessage();
                continue;
            }
            $qualities[] = [
                'id' => $quality->id,
            ];
        }
        $this->character->qualities = $qualities;
        return $this;
    }

    protected function loadSkills(): Shadowrun5eConverter
    {
        $skills = [];
        foreach ($this->xml->newskills->skills->skill as $skill) {
            $level = (int)$skill->base;
            if (0 === $level) {
                // Character doesn't have the skill.
                continue;
            }

            $name = (string)$skill->name;

            $spec = null;
            if (isset($skill->specs)) {
                // Skill has at least one specialization.
                $specs = [];
                foreach ($skill->specs->spec as $value) {
                    $specs[] = (string)$value->name;
                }
                $spec = implode(',', $specs);
            }

            try {
                $skills[] = [
                    'id' => ActiveSkill::findIdByName($name),
                    'level' => $level,
                    'specialization' => $spec,
                ];
            } catch (RuntimeException) {
                $this->errors[] = sprintf('Unable to find skill "%s"', $name);
            }
        }
        $this->character->skills = $skills;
        return $this;
    }

    protected function loadSpells(): Shadowrun5eConverter
    {
        if (!$this->isMagician) {
            return $this;
        }

        $magics = $this->character->magics;
        $magics['spells'] = [];
        foreach ($this->xml->spells->spell as $spell) {
            try {
                $spell = new Spell(
                    $this->createIDFromName((string)$spell->name)
                );
                $magics['spells'][] = $spell->id;
            } catch (RuntimeException $ex) {
                $this->errors[] = $ex->getMessage();
            }
        }
        $this->character->magics = $magics;
        return $this;
    }

    protected function loadTradition(): Shadowrun5eConverter
    {
        if (!$this->isMagician) {
            return $this;
        }
        $id = $this->createIDFromName((string)$this->xml->tradition->name);
        try {
            new Tradition($id);
            $magics = $this->character->magics;
            $magics['tradition'] = $id;
        } catch (RuntimeException $ex) {
            $this->errors[] = $ex->getMessage();
        }
        return $this;
    }

    protected function loadWeapons(): Shadowrun5eConverter
    {
        $weapons = [];
        foreach ($this->xml->weapons->weapon as $rawWeapon) {
            if ('Unarmed Attack' === (string)$rawWeapon->name) {
                continue;
            }
            try {
                $weapon = Weapon::findByName((string)$rawWeapon->name);
            } catch (RuntimeException $ex) {
                $this->errors[] = $ex->getMessage();
                continue;
            }
            $weapons[] = [
                'id' => $weapon->id,
            ];
        }
        $this->character->weapons = $weapons;
        return $this;
    }

    /**
     * Convert a Chummer 5 file to a Commlink character and return it.
     * @return Character
     */
    public function convert(): Character
    {
        $this->isAdept = 'True' === (string)$this->xml->adept;
        $this->isMagician = 'True' === (string)$this->xml->magician;
        $this->isTechnomancer = 'True' === (string)$this->xml->technomancer;

        $this->loadArmor()
            ->loadAttributes()
            ->loadAugmentations()
            ->loadContacts()
            ->loadGear()
            ->loadIdentities()
            ->loadKnowledgeSkills()
            ->loadMentorSpirit()
            ->loadMetadata()
            ->loadPriorities()
            ->loadQualities()
            ->loadSkills()
            ->loadSpells()
            ->loadTradition()
            ->loadWeapons();
        return $this->character;
    }
}
