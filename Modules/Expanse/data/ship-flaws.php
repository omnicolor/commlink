<?php

declare(strict_types=1);

return [
    /*
    '' => [
        'description' => '',
        'effects' => [],
        'name' => '',
        'page' => ,
        'ruleset' => '',
    ],
     */
    'bad-juice' => [
        'description' => 'The ship’s juice is not of the highest quality, imposing a –1 penalty to Constitution tests involving accelera- tion hazards.',
        'name' => 'Bad juice',
        'page' => 123,
        'ruleset' => 'core',
    ],
    'faulty-system' => [
        'description' => 'One of the ship’s systems does not work as reliably as it should. The first time in an encounter when it is important that the system works, roll a die: On a 1 or 2, the system stops working, just like a loss due to damage (see Losses under Space Combat) and it requires a similar damage control effort to get it working again (a TN 11 advanced Intelligence (Engineering) test with a success threshold of 5). Until repaired, the non-functional system cannot be used',
        'name' => 'Faulty system',
        'page' => 124,
        'ruleset' => 'core',
    ],
];
