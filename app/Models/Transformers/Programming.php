<?php

declare(strict_types=1);

namespace App\Models\Transformers;

enum Programming: string
{
    case Engineer = 'engineer';
    case Gunner = 'gunner';
    case Scout = 'scout';
    case Warrior = 'warrior';

    /**
     * @return array<string, Action>
     */
    public function actions(): array
    {
        return match ($this) {
            Programming::Engineer => [
                'courage' => Action::Materials,
                'endurance' => Action::Construction,
                'firepower' => Action::Assault,
                'intelligence' => Action::Invention,
                'rank' => Action::Transform,
                'skill' => Action::Repair,
                'speed' => Action::Data,
                'strength' => Action::MeleeAttack,
            ],
            Programming::Gunner => [
                'courage' => Action::Trooper,
                'endurance' => Action::Defence,
                'firepower' => Action::Assault,
                'intelligence' => Action::Demolition,
                'rank' => Action::Transform,
                'skill' => Action::Accuracy,
                'speed' => Action::Support,
                'strength' => Action::MeleeAttack,
            ],
            Programming::Scout => [
                'courage' => Action::Espionage,
                'endurance' => Action::Sabotage,
                'firepower' => Action::Assault,
                'intelligence' => Action::Surveillance,
                'rank' => Action::Transform,
                'skill' => Action::Communication,
                'speed' => Action::Dash,
                'strength' => Action::MeleeAttack,
            ],
            Programming::Warrior => [
                'courage' => Action::Intercept,
                'endurance' => Action::Defence,
                'firepower' => Action::Assault,
                'intelligence' => Action::Strategy,
                'rank' => Action::Transform,
                'skill' => Action::Acrobatics,
                'speed' => Action::Dash,
                'strength' => Action::MeleeAttack,
            ],
        };
    }

    public function description(): string
    {
        return match ($this) {
            Programming::Engineer => 'As the least combat-oriented of the '
                . 'Functions, the Engineer is in the minority at only 18% of '
                . 'G1 Transformers. However, what Statistics are high for the '
                . 'Robot drastically affects the direction of an Engineer, and '
                . 'is the reason why Ratchet (with a high Repair), Mixmaster '
                . '(with a high Construction), and Megatron (with a high '
                . 'Invention) co-exist in this Funtion. Engineers have the '
                . 'capacity for combat, but need to be supplemented with a '
                . 'wise Alt.Mode choice to facilitate that goal.',
            Programming::Gunner => 'Comprising 19% of all G1 Transformers, '
                . 'Gunners are the main support Function. The Support Action '
                . 'makes them great for in-team situations, and their Trooper '
                . 'Action makes them excellent to strike off on their own. '
                . 'Gunners are slow, but traditionally the toughtest and most '
                . 'dependable of the Transformers, with staying-power in a '
                . 'fight.',
            Programming::Scout => 'Where the Engineer forces creativity on the '
                . 'Map, Scouts have creativity in the field. They are 24% of '
                . 'all G1 Transformers, the second most popular after the '
                . 'Warrior. This is because of their wide range of Actions and '
                . 'the availability of Dash helps them to get into situations '
                . 'without being detected, do whatever needs to be done, and '
                . 'get out quickly. Scouts are a combat-alternative that is '
                . 'still capable of front-line combat, although to a lesser '
                . 'extent that the Warrior or Gunner.',
            Programming::Warrior => 'The Warrior is the bedrock of both Robot '
                . 'armies, comprising 43% of G1 Transformers. This is the '
                . 'standard, dependable Function, the easiest to use, most '
                . 'understandable, and well-rounded. With the right Stats, it '
                . 'can also be the most versatile.',
        };
    }
}
