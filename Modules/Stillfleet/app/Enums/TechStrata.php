<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Enums;

/**
 * @codeCoverageIgnore
 */
enum TechStrata: string
{
    case Bio = 'bio';
    case Bug = 'bug';
    case Clank = 'clank';
    case Code = 'code';
    case Escheresque = 'escheresque';
    case Force = 'force';
    case Nano = 'nano';

    public function description(): string
    {
        return match ($this) {
            self::Bio => 'Biotechnologies including pharmaceuticals. Many '
                . 'civilizations pursue biotech in order to improve crops, '
                . 'manufacture materials efficiently, and extend lives. This '
                . 'category covers contemporary-for-your-characters brewing '
                . '(the Co. ferments your food in vitro using engineered '
                . 'microbes) as well as medicines. Common on Spindle; '
                . 'uncommon on the provinces (expensive).',
            self::Bug => 'Bioelectrochemical–quantum technologies. The height '
                . 'of bug tech was 50 million years (MY) ago during the reign '
                . 'of the cryptocerids and will be reached again in another '
                . '175 MY by the benevolent heechee (bee-people). The '
                . 'technologies of the crab-fungi called the mi-go are '
                . 'classed as bug, since they require plugging in one’s '
                . 'antennae to activate. (Although these systems can '
                . 'sometimes be used “dead,” in which case they are classed '
                . 'as bio.) Legendary.',
            self::Clank => 'Mechanical technologies, from ploughs and swords '
                . 'to steamships and guns. All civilizations have some '
                . 'mechanical tech. To most provincials, “technology” means '
                . 'wood, leather, and metal. Common.',
            self::Code => 'Electronic and information technologies including '
                . 'digital computers (comms). The civilization most often '
                . 'associated with code, on-Spin, is that of the Ancient '
                . 'ur-humans of Terra [us!]. Rare.',
            self::Escheresque => 'Extradimensional “technologies”—a likely '
                . 'inapt term—are difficult to even imagine. These include '
                . 'items native to the Escheresque, the organs of the '
                . 'grylloids of Luna’s Farside, and the “gifts” of the Old '
                . 'Ones (the various cthulhicate entities that hell '
                . 'scientists sometimes foolishly summon). Unbelievable.',
            self::Force => 'Electromagnetic, piezoelectric, electrogravitic, '
                . 'and solar technologies, including laser weapons and '
                . 'vehicles capable of extrasolar voyages. Eight thousand '
                . 'years ago, the High Tephnians dominated Terra with force '
                . 'technologies. Very rare.',
            self::Nano => 'Nanotechnologies, including nanobiotech. Six '
                . 'thousand years ago, the Late Tephnians created stiffworks '
                . 'powered by nanites and then traveled to many Goldilocks '
                . 'worlds. The Snakemen, who will rule earth some time in the '
                . 'future but journey back in time occasionally for terrible '
                . 'purposes, (will) also employ nanites. Treasured.',
        };
    }

    public function dongle(): string
    {
        return match ($this) {
            self::Bio => 'The bio dongle is a portable wet lab—a '
                . 'backpacksized refrigerator for culturing microbes, storing '
                . 'plasmids and reagents, testing model organisms '
                . '(mouse-sized albino cave crickets), etc.',
            self::Bug => 'The bug dongle is a combined cryptocerid pheromone '
                . 'sniffer, proteome mangler, and biophoton emitter—a '
                . 'hand-sized, black, spider-like organ that plugs into '
                . 'living and machinic bodies, filling them with synesthetic '
                . 'memories of the extinct ultra-roaches. A pheromone sniffer '
                . 'is itself a highly coveted archaetech item.',
            self::Clank => 'The clank dongle—the kit that allows for creating '
                . 'mechanical technologies—is a hand tool kit, including a '
                . 'wrench, lathe, hammer, nails, measuring tape, etc.',
            self::Code => 'The code dongle is a comm—a small future computer '
                . 'or smartphone.',
            self::Escheresque => 'The Escheresque stratum has no dongle, '
                . 'other than a fragile, sapient consciousness that can be '
                . 'touched by the radically open, utterly vile Outside.',
            self::Force => 'The force dongle is a laser tool kit—voltmeter, '
                . 'laser wrench, sonic screwdriver, micro-welder, etc.',
            self::Nano => 'The nano dongle is an atomic force microscope, for '
                . 'moving around nanites, attached to a backpacksized '
                . 'nanoprinter.',
        };
    }
}
