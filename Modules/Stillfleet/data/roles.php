<?php

declare(strict_types=1);

return [
    /*
    [
        'description' => '',
        'grit' => '["",""]',
        'name' => '',
        'optional_choices' => ,
        'page' => ,
        'power_advanced' => '[""]',
        'power_marquee' => '',
        'power_optional' => '["",""]',
        'power_other' => '["",""]',
        'responsibilities' => '["",""]',
        'ruleset' => 'core',
    ],
     */
    [
        'id' => 'banshee',
        'description' => 'You are an engineer—and very likely a daredevil—trained by the Archivists to understand the beyond-human science of stiffworks.',
        'grit' => '["movement","reason"]',
        'name' => 'Banshee',
        'optional_choices' => 1,
        'page' => 43,
        'power_advanced' => '["communications"]',
        'power_marquee' => 'tack',
        'power_optional' => '["astrogate","interface","power-up","reposition"]',
        'power_other' => '["dive","jack"]',
        'responsibilities' => '["opening stiffworks (quantum gates connecting disparate points in timespaces);","appraising and, as needed, repairing technologies discovered during the venture;","assisting the venture’s pir in the collection of technoscientific samples for the Archive."]',
        'ruleset' => 'core',
    ],
    [
        'id' => 'tremulant',
        'description' => 'You are a trained hell scientist—a Terran conscript who was tortured for years by the Archivists in order to allow you to control the Weird.',
        'grit' => '["charm","-combat","reason","will"]',
        'name' => 'Tremulant',
        'optional_choices' => 1,
        'page' => 65,
        'power_advanced' => '["immerphysics"]',
        'power_marquee' => 'control-hell-science',
        'power_optional' => '["call-weird","extend-self","feel-weird","pass","phase"]',
        'power_other' => '["drain","memorize"]',
        'responsibilities' => '["using your command of the hell science to complete the venture as contracted","identifying and, as needed, eliminating Weird threats to the venture","conducting certain necessary tasks for the Archive"]',
        'ruleset' => 'core',
    ],
];
