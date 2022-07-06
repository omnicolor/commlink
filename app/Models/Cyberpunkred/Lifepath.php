<?php

declare(strict_types=1);

namespace App\Models\Cyberpunkred;

use RuntimeException;

class Lifepath
{
    /**
     * @var array<int, string>
     */
    public static array $affectations = [
        1 => 'Tattoos',
        2 => 'Mirrorshades',
        3 => 'Ritual scars',
        4 => 'Spiked gloves',
        5 => 'Nose rings',
        6 => 'Tongue or other piercings',
        7 => 'Strange fingernail implants',
        8 => 'Spiked boots or heels',
        9 => 'Fingerless gloves',
        10 => 'Strange contacts',
    ];

    /**
     * @var array<int, array<string, string>>
     */
    public static array $backgrounds = [
        1 => [
            'name' => 'Corporate execs',
            'description' => 'Wealthy, powerful, with servants, luxury homes, '
                . 'and the best of everything. Private security made sure you '
                . 'were always safe. You definitely went to a big-name private '
                . 'school.',
        ],
        2 => [
            'name' => 'Corporate managers',
            'description' => 'Well to do, with large homes, safe '
                . 'neighborhoods, nice cars, etc. Sometimes your parent(s) '
                . 'would hire servants, although this was rare. You had a mix '
                . 'of private and corporate education.',
        ],
        3 => [
            'name' => 'Corporate technicians',
            'description' => 'Middle-middle class, with comfortable conapts or '
                . 'Beaverville suburban homes, minivans and corporate-run '
                . 'technical schools. Kind of like living 1950s America '
                . 'crossed with 1984.',
        ],
        4 => [
            'name' => 'Nomad pack',
            'description' => 'You had a mix of rugged trailers, vehicles, and '
                . 'huge road kombis for your home. You learned to drive and '
                . 'fight at an early age, but the family was always there to '
                . 'care for you. Food was actually fresh and abundant. Mostly '
                . 'home schooled.',
        ],
        5 => [
            'name' => 'Ganger "Family"',
            'description' => 'A savage, violent home in any place the gang '
                . 'could take over. You were usually hungry, cold, and scared. '
                . 'You probably didn\'t know who your actual parents were. '
                . 'Education? The Gang taught you how to fight, kill, and '
                . 'steal—what else did you need to know?',
        ],
        6 => [
            'name' => 'Combat zoners',
            'description' => 'A step up from a gang "family," your home was a '
                . 'decaying building somewhere in the ‘Zone\', heavily '
                . 'fortified. You were hungry at times, but regularly could '
                . 'score a bed and a meal. Home schooled.',
        ],
        7 => [
            'name' => 'Urban homeless',
            'description' => 'You lived in cars, dumpsters, or abandoned '
                . 'shipping modules. If you were lucky. You were usually '
                . 'hungry, cold, and scared, unless you were tough enough to '
                . 'fight for the scraps. Education? School of Hard Knocks.',
        ],
        8 => [
            'name' => 'Megastructure warren rats',
            'description' => 'You grew up in one of the huge new '
                . 'megastructures that went up after the War. A tiny conapt, '
                . 'kibble and scop for food, a mostly warm bed. Some better '
                . 'educated adult warren dwellers or a local Corporation may '
                . 'have set up a school.',
        ],
        9 => [
            'name' => 'Reclaimers',
            'description' => 'You started out on the road, but then moved into '
                . 'one of the deserted ghost towns or cities to rebuild it. A '
                . 'pioneer life: dangerous, but with plenty of simple food and '
                . 'a safe place to sleep. You were home schooled if there was '
                . 'anyone who had the time.',
        ],
        10 => [
            'name' => 'Edgerunners',
            'description' => 'Your home was always changing based on your '
                . 'parents\' current "job." Could be a luxury apartment, an '
                . 'urban conapt, or a dumpster if you were on the run. Food '
                . 'and shelter ran the gamut from gourmet to kibble.',
        ],
    ];

    /**
     * @var array<int, string>
     */
    public static array $clothes = [
        1 => 'Generic Chic (Standard, Colorful, Modular)',
        2 => 'Leisureweat (Comfort, Agility, Athleticism)',
        3 => 'Urban Flash (Flashy, Technological, Streetwear)',
        4 => 'Businesswear (Leadership, Presence, Authority)',
        5 => 'High Fashion (Exclusive, Designer, Couture)',
        6 => 'Bohemian (Folksy, Retro, Free-spirited)',
        7 => 'Bag Lady Chic (Homeless, Ragged, Vagrant)',
        8 => 'Gang Colors (Dangerous, Violent, Rebellious)',
        9 => 'Nomad Leathers (Western, Rugged, Tribal)',
        10 => 'Asia Pop (Bright, Costume-like, Youthful)',
    ];

    /**
     * @var array<int, string>
     */
    public static array $environments = [
        1 => 'Ran on The Street, with no adult supervision.',
        2 => 'Spent in a safe Corp Zone walled off from the rest of the City.',
        3 => 'In a Nomad pack moving from place to place.',
        4 => 'In a Nomad pack with roots in transport (ships, planes, '
            . 'caravans).',
        5 => 'In a decaying, once upscale neighborhood, now holding off the '
            . 'boosters to survive.',
        6 => 'In the heart of the Combat Zone, living in a wrecked building or '
            . 'other squat.',
        7 => 'In a huge "megastructure" building controlled by a Corp or the '
            . 'City.',
        8 => 'In the ruins of a deserted town or city taken over by '
            . 'Reclaimers.',
        9 => 'In a Drift Nation (a floating offshore city) that is a meeting '
            . 'place for all kinds of people.',
        10 => 'In a Corporate luxury "starscraper", high above the rest of the '
            . 'teeming rabble.',
    ];

    /**
     * @var array<int, string>
     */
    public static array $familyCrisises = [
        1 => 'Your family lost everything through betrayal.',
        2 => 'Your family lost everything through bad management.',
        3 => 'Your family was exiled or otherwise driven from their original '
            . 'home/nation/Corporation.',
        4 => 'Your family is imprisoned, and you alone escaped.',
        5 => 'Your family vanished. You are the only remaining member.',
        6 => 'Your family was killed, and you were the only survivor.',
        7 => 'Your family is involved in a long-term conspiracy, organization, '
            . 'or association, such as a crime family or revolutionary group.',
        8 => 'Your family was scattered to the winds due to misfortune.',
        9 => 'Your family is cursed with a hereditary feud that has lasted for '
            . 'generations.',
        10 => 'You are the inheritor of a family debt; you must honor this '
            . 'debt before moving on with your life.',
    ];

    /**
     * @var array<int, string>
     */
    public static array $feelings = [
        1 => 'I stay neutral.',
        2 => 'I stay neutral.',
        3 => 'I like almost everyone.',
        4 => 'I hate almost everyone.',
        5 => 'People are tools. Use them for your own goals then discard them.',
        6 => 'Every person is a valuable individual.',
        7 => 'People are obstacles to be destroyed if they cross me.',
        8 => 'People are untrustworthy. Don‘t depend on anyone.',
        9 => 'Wipe ‘em all out and let the cockroaches take over.',
        10 => 'People are wonderful!',
    ];

    /**
     * @var array<int, string>
     */
    public static array $hairStyles = [
        1 => 'Mohawk',
        2 => 'Long and ratty',
        3 => 'Short and spiked',
        4 => 'Wild and all over',
        5 => 'Bald',
        6 => 'Striped',
        7 => 'Wild colors',
        8 => 'Neat and short',
        9 => 'Short and curly',
        10 => 'Long and straight',
    ];

    /**
     * @var array<int, array<string, string|array<int, string>>>>
     */
    public static array $origins = [
        1 => [
            'name' => 'North American',
            'languages' => [
                'Chinese', 'Cree', 'Creole', 'English', 'French', 'Navajo', 'Spanish',
            ],
        ],
        2 => [
            'name' => 'South/Central American',
            'languages' => [
                'Creole', 'English', 'German', 'Guarani', 'Mayan', 'Portuguese', 'Quechua', 'Spanish',
            ],
        ],
        3 => [
            'name' => 'Western European',
            'languages' => [
                'Dutch', 'English', 'French', 'German', 'Italian', 'Norwegian', 'Portuguese', 'Spanish',
            ],
        ],
        4 => [
            'name' => 'Eastern European',
            'languages' => [
                'English', 'Finnish', 'Polish', 'Romanian', 'Russian', 'Ukrainian',
            ],
        ],
        5 => [
            'name' => 'Middle Eastern/North African',
            'languages' => [
                'Arabic', 'Berber', 'English', 'Farsi', 'French', 'Hebrew', 'Turkish',
            ],
        ],
        6 => [
            'name' => 'Sub-Saharan African',
            'languages' => [
                'Arabic', 'English', 'French', 'Hausa', 'Lingala', 'Oromo', 'Portuguese', 'Swahili', 'Twi', 'Yoruba',
            ],
        ],
        7 => [
            'name' => 'South Asian',
            'languages' => [
                'Bengali', 'Dari', 'English', 'Hindi', 'Nepali', 'Sinhalese', 'Tamil', 'Urdu',
            ],
        ],
        8 => [
            'name' => 'South East Asian',
            'languages' => [
                'Arabic', 'Burmese', 'English', 'Filipino', 'Hindi', 'Indonesian', 'Khmer', 'Malayan', 'Vietnamese',
            ],
        ],
        9 => [
            'name' => 'East Asian',
            'languages' => [
                'Cantonese Chinese', 'English', 'Japanese', 'Korean', 'Mandarin Chinese', 'Mongolian',
            ],
        ],
        10 => [
            'name' => 'Oceania/Pacific Islander',
            'languages' => [
                'English', 'French', 'Hawaiian', 'Maori', 'Pama-Nyungan', 'Tahitian',
            ],
        ],
    ];

    /**
     * @var array<int, string>
     */
    public static array $personalities = [
        1 => 'Shy and secretive',
        2 => 'Rebellious, antisocial, and violent',
        3 => 'Arrogant, proud, and aloof',
        4 => 'Moody, rash, and headstrong',
        5 => 'Picky, fussy, and nervous',
        6 => 'Stable and serious',
        7 => 'Silly and fluff-headed',
        8 => 'Sneaky and deceptive',
        9 => 'Intellectual and detached',
        10 => 'Friendly and outgoing',
    ];

    /**
     * @var array<int, string>
     */
    public static array $persons = [
        1 => 'A parent',
        2 => 'A brother or sister',
        3 => 'A lover',
        4 => 'A friend',
        5 => 'Yourself',
        6 => 'A pet',
        7 => 'A teacher or mentor',
        8 => 'A public figure',
        9 => 'A personal hero',
        10 => 'No one',
    ];

    /**
     * @var array<int, string>
     */
    public static array $possessions = [
        1 => 'A weapon',
        2 => 'A tool',
        3 => 'A piece of clothing',
        4 => 'A photograph',
        5 => 'A book or diary',
        6 => 'A recording',
        7 => 'A musical instrument',
        8 => 'A piece of jewelry',
        9 => 'A toy',
        10 => 'A letter',
    ];

    /**
     * @var array<int, string>
     */
    public static array $values = [
        1 => 'Money',
        2 => 'Honor',
        3 => 'Your word',
        4 => 'Honesty',
        5 => 'Knowledge',
        6 => 'Vengeance',
        7 => 'Love',
        8 => 'Power',
        9 => 'Family',
        10 => 'Friendship',
    ];

    /**
     * Constructor.
     * @param array<string, array<string, int>> $lifepath
     */
    public function __construct(protected array $lifepath)
    {
    }

    public function getAffectation(): string
    {
        if (!isset($this->lifepath['affectation']['chosen'])) {
            throw new RuntimeException('Lifepath value not set');
        }
        return self::$affectations[$this->lifepath['affectation']['chosen']];
    }

    /**
     * @return array<string, string>
     */
    public function getBackground(): array
    {
        if (!isset($this->lifepath['background']['chosen'])) {
            throw new RuntimeException('Lifepath value not set');
        }
        return self::$backgrounds[$this->lifepath['background']['chosen']];
    }

    public function getClothing(): string
    {
        if (!isset($this->lifepath['clothing']['chosen'])) {
            throw new RuntimeException('Lifepath value not set');
        }
        return self::$clothes[$this->lifepath['clothing']['chosen']];
    }

    public function getEnvironment(): string
    {
        if (!isset($this->lifepath['environment']['chosen'])) {
            throw new RuntimeException('Lifepath value not set');
        }
        return self::$environments[$this->lifepath['environment']['chosen']];
    }

    public function getFamilyCrisis(): string
    {
        if (!isset($this->lifepath['family-crisis']['chosen'])) {
            throw new RuntimeException('Lifepath value not set');
        }
        return self::$familyCrisises[$this->lifepath['family-crisis']['chosen']];
    }

    public function getFeeling(): string
    {
        if (!isset($this->lifepath['feeling']['chosen'])) {
            throw new RuntimeException('Lifepath value not set');
        }
        return self::$feelings[$this->lifepath['feeling']['chosen']];
    }

    public function getHairStyle(): string
    {
        if (!isset($this->lifepath['hair']['chosen'])) {
            throw new RuntimeException('Lifepath value not set');
        }
        return self::$hairStyles[$this->lifepath['hair']['chosen']];
    }

    /**
     * @return array<string, string|array<int, string>>>
     */
    public function getOrigin(): array
    {
        if (!isset($this->lifepath['origin']['chosen'])) {
            throw new RuntimeException('Lifepath value not set');
        }
        return self::$origins[$this->lifepath['origin']['chosen']];
    }

    public function getPerson(): string
    {
        if (!isset($this->lifepath['person']['chosen'])) {
            throw new RuntimeException('Lifepath value not set');
        }
        return self::$persons[$this->lifepath['person']['chosen']];
    }

    public function getPersonality(): string
    {
        if (!isset($this->lifepath['personality']['chosen'])) {
            throw new RuntimeException('Lifepath value not set');
        }
        return self::$personalities[$this->lifepath['personality']['chosen']];
    }

    public function getPossession(): string
    {
        if (!isset($this->lifepath['possession']['chosen'])) {
            throw new RuntimeException('Lifepath value not set');
        }
        return self::$possessions[$this->lifepath['possession']['chosen']];
    }

    public function getValues(): string
    {
        if (!isset($this->lifepath['value']['chosen'])) {
            throw new RuntimeException('Lifepath value not set');
        }
        return self::$values[$this->lifepath['value']['chosen']];
    }
}
