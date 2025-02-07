<?php

declare(strict_types=1);

namespace Modules\Avatar\Models;

/**
 * @codeCoverageIgnore
 */
enum Training: string
{
    case Airbending = 'Airbending';
    case Earthbending = 'Earthbending';
    case Firebending = 'Firebending';
    case Technology = 'Technology';
    case Waterbending = 'Waterbending';
    case Weapons = 'Weapons';

    public function description(): string
    {
        return match ($this) {
            Training::Airbending => 'Swiping air upwards to deflect incoming '
                . 'arrows, pulling air around a weapon to disarm someone, '
                . 'decreasing one’s own air resistance to outmaneuver a '
                . 'stronger foe—airbending warriors use their element to '
                . 'defend themselves and redirect hostile energy. An Airbender '
                . 'might be a cautious pacifist who enhances their speed to '
                . 'avoid danger and exhaust enemies, or a more proactive '
                . 'protector who employs bursts of wind to control the '
                . 'battlefield. An Airbender might wear flowing clothes to '
                . 'create air ripples they can volley at attackers, or carry a '
                . 'special item or tool to focus their bending, like using a '
                . 'flute to focus air jets and amplify sound vibrations.',
            Training::Earthbending => 'Levitating stones to hurl them into '
                . 'obstacles, encasing one’s body in a protective shell of '
                . 'earth, transmuting earth to quicksand to immobilize an '
                . 'enemy—earthbending warriors often bide their time, using '
                . 'their element to defend until the perfect moment to '
                . 'counterattack. An Earthbender might be a durable defender '
                . 'with slow and deliberate strikes, or they might sunder the '
                . 'earth to disorient and separate their foes. Some '
                . 'Earthbenders prefer to go barefoot to stay connected to '
                . 'their element; others carry tools like earthen discs or '
                . 'stone gauntlets to have something to bend nearby.',
            Training::Firebending => 'Slicing through a barrier with a blade '
                . 'of flame, pinning enemies behind cover by unleashing a '
                . 'concentrated fire stream, driving an opponent away with a '
                . 'series of fireballs—firebending warriors manipulate their '
                . 'chi and ambient fire with intense and aggressive results. A '
                . 'Firebender might prefer to barrage their foes from afar '
                . 'with precise fire bolts, or mix close punches and kicks '
                . 'with flame bursts to take the fight directly to the enemy. '
                . 'Because Firebenders manipulate their own energy, they don’t '
                . 'need to access their element from their immediate '
                . 'environment and are always “armed.”',
            Training::Technology => 'Setting jury-rigged traps and snares, '
                . 'hurling flasks of alchemical concoctions, engaging enemies '
                . 'with selfmade electrified weapons—a technology-based '
                . 'warrior uses their expertise with devices and machines to '
                . 'engage foes and resolve threats. A technological warrior '
                . 'might be an eager grease monkey with a love for '
                . 'deconstructing technology, a trapper adapting their '
                . 'survival skills on the fly, or a military engineer who '
                . 'wields and maintains advanced weapons and armor. While some '
                . 'weapon-using characters might also carry advanced '
                . 'weapons—like an electrified glove—they lack the expertise '
                . 'to build and repair these machines. Technological warriors '
                . 'might use a single complex device with many effects, carry '
                . 'the tools they need to create devices on the fly, or use '
                . 'simpler tools to devastating effect.',
            Training::Waterbending => 'Weaving water into snapping and '
                . 'slashing whips, manipulating one’s breath into clouds of '
                . 'freezing ice, sculpting liquid into a defensive '
                . 'shield—waterbending warriors manipulate their element with '
                . 'fluidity and grace. A Waterbender might defend their allies '
                . 'by creating liquid barriers to freeze weapons and '
                . 'attackers, or they might be an aggressive warrior who '
                . 'unleashes torrential water jets or concealable weapons made '
                . 'of ice. Not all Waterbenders are warriors—some have healing '
                . 'powers as well. A Waterbender might carry a waterskin with '
                . 'them to have something to bend at all times, or prefer to '
                . 'use nearby liquids instead—some Waterbenders can even use '
                . 'their sweat.',
            Training::Weapons => 'Raining arrows down on opponents, pulling '
                . 'off a dangerous trick with a boomerang, deflecting blows '
                . 'with bare hands—weapon warriors are martial experts who can '
                . 'hold their own against benders. This training can represent '
                . 'any martial character who isn’t a bender—duelists, archers, '
                . 'unarmed chi blockers, and more. A weapon warrior might be '
                . 'an amateur boxer fighting in seedy bars, a person '
                . 'transformed by a spirit, or a member of an ancient martial '
                . 'order. Weapon warriors might carry a variety of weapons '
                . 'appropriate to their style and era, or they might wield '
                . 'a single weapon so masterfully it’s an extension of '
                . 'themselves.',
        };
    }
}
