<?php

declare(strict_types=1);

namespace Modules\Transformers\Models;

enum Action
{
    case Accuracy;
    case Acrobatics;
    case Assault;
    case Communication;
    case Construction;
    case Dash;
    case Data;
    case Defence;
    case Demolition;
    case Espionage;
    case Intercept;
    case Invention;
    case Materials;
    case MeleeAttack;
    case Repair;
    case Sabotage;
    case Strategy;
    case Support;
    case Surveillance;
    case Transform;
    case Trooper;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function description(): string
    {
        return match ($this) {
            Action::Accuracy => 'Target a specific range, object, or area of '
                . 'attack within one square. Example: held item blasted out of '
                . 'the enemy\'s hand.',
            Action::Acrobatics => 'Jumping, leaping, tumbling, balance, '
                . 'reflexes, and landing without taking damage. Situations of '
                . 'great agility. Does not take the place of Defence. Ex: '
                . 'Jumping hurdles but keeping pace with normal movement.',
            Action::Assault => 'Fire at a distance using an equipped weapon, '
                . 'attached, mounted, or held.',
            Action::Communication => 'Free: Broadcast to all allies on the '
                . 'battlefield. Free: Detect spionage (Stealth) (R). Turn: '
                . 'Break jamming signal. Turn: Contact home base with a '
                . 'computer terminal.',
            Action::Construction => 'Creation of structures, machinery, '
                . 'and complex devices from Invention designs. Examples: '
                . 'Construct a bunker to use for cover, or a ray gun from '
                . 'plans.',
            Action::Dash => 'Roll for movement, with success equal to the '
                . 'maximum number of squares moveable in a full Turn move, or '
                . '1/2 that and Act. If < 3, can still use basic movement of 3 '
                . 'squares or 1 square and an Action.',
            Action::Data => 'Know HP, Energon, History, Materials, Function, '
                . 'etc., of one visible target.',
            Action::Defence => 'Roll an additional dice for Endurance, and '
                . 'take the better result to block, endure, or otherwise not '
                . 'be hit or damaged.',
            Action::Demolition => 'Free: Theorise so that no damage is taken '
                . 'to self when ramming (R). Turn: If successful in the roll, '
                . 'use Melee Attack to damage and push the opponent back to '
                . 'the maximum end of your movement’s length.',
            Action::Espionage => 'Stealth, to be undetectable by unfriendly '
                . 'sensors. Turn: Interrogation.',
            Action::Intercept => 'Gain one additional square\'s movement to '
                . 'catch up with a story-goal, head off an opponent, get away, '
                . 'or generally out-run someone. Turn-urgent, such as in '
                . 'direct combat.',
            Action::Invention => 'Draw from applicable Materials to create '
                . 'designs for new weapons, items, tools, gadgets, and dodads. '
                . 'Example: a Gun that shrinks you down to a fraction of Size '
                . '0.',
            Action::Materials => 'Interact with an applicable Map square to '
                . 'gain the components for Invention, major Repairs, or '
                . 'Espionage. Example: find a discarded Med Pack in Wreckage, '
                . 'or a Processor Speed Chip.',
            Action::MeleeAttack => 'Attack one adjacent or diagonal square '
                . 'distance by punching (1d6/2 DMG) or using a Melee Weapon '
                . 'such as a Sword, claws, teeth, foot stomping, clapping, '
                . 'kicking, etc.',
            Action::Repair => 'Restore HP to anything metal or to one '
                . 'Statistic. HP+ = Success # rolled. Major Repairs require '
                . 'Materials for parts. Repair auto-cancels some Weapon '
                . 'Effects.',
            Action::Sabotage => 'Affect a Map Item, Computer, or other '
                . 'technological situation in play to set a trap for '
                . 'activation. Example: Rig a communications console to '
                . 'explode when activated.',
            Action::Strategy => 'Project a reasonable scenario of events and '
                . 'essentially create its odds. Example: Destroying a section '
                . 'of a bridge to make sure of where it falls.',
            Action::Support => 'Roll to aid in another’s Action or repeat it '
                . 'for yourself. Example: Roll Support to help Repair when an '
                . 'adjacent Engineer did just that on their turn.',
            Action::Surveillance => 'Visual, audio or other receptors identify '
                . 'or clarify sensory events and relevant information. '
                . 'Overcomes Espionage. Also used for between-battle scouting '
                . 'missions.',
            Action::Transform => 'Once per Turn, switch between Modes. Does '
                . 'not forgo Energon Gain.',
            Action::Trooper => 'Summon the vigor to restore Energon for '
                . 'yourself only. Energon+ = Success # rolled.',
        };
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<int, Programming>
     */
    public function programming(): array
    {
        return match ($this) {
            Action::Accuracy => [Programming::Gunner],
            Action::Acrobatics => [Programming::Warrior],
            Action::Assault => [
                Programming::Engineer,
                Programming::Gunner,
                Programming::Scout,
                Programming::Warrior,
            ],
            Action::Communication => [Programming::Scout],
            Action::Construction => [Programming::Engineer],
            Action::Dash => [
                Programming::Scout,
                Programming::Warrior,
            ],
            Action::Data => [Programming::Engineer],
            Action::Defence => [
                Programming::Gunner,
                Programming::Warrior,
            ],
            Action::Demolition => [Programming::Gunner],
            Action::Espionage => [Programming::Scout],
            Action::Intercept => [Programming::Warrior],
            Action::Invention => [Programming::Engineer],
            Action::Materials => [Programming::Engineer],
            Action::MeleeAttack => [
                Programming::Engineer,
                Programming::Gunner,
                Programming::Scout,
                Programming::Warrior,
            ],
            Action::Repair => [Programming::Engineer],
            Action::Sabotage => [Programming::Scout],
            Action::Strategy => [Programming::Warrior],
            Action::Support => [Programming::Gunner],
            Action::Surveillance => [Programming::Scout],
            Action::Transform => [
                Programming::Engineer,
                Programming::Gunner,
                Programming::Scout,
                Programming::Warrior,
            ],
            Action::Trooper => [Programming::Gunner],
        };
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function statistic(): string
    {
        return match ($this) {
            Action::Accuracy => 'skill',
            Action::Acrobatics => 'skill',
            Action::Assault => 'firepower',
            Action::Communication => 'skill',
            Action::Construction => 'endurance',
            Action::Dash => 'speed',
            Action::Data => 'speed',
            Action::Defence => 'endurance',
            Action::Demolition => 'intelligence',
            Action::Espionage => 'courage',
            Action::Intercept => 'courage',
            Action::Invention => 'intelligence',
            Action::Materials => 'courage',
            Action::MeleeAttack => 'strength',
            Action::Repair => 'skill',
            Action::Sabotage => 'endurance',
            Action::Strategy => 'intelligence',
            Action::Support => 'speed',
            Action::Surveillance => 'intelligence',
            Action::Transform => 'rank',
            Action::Trooper => 'courage',
        };
    }
}
