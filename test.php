<?php

use App\Console\Irc;

require __DIR__ . '/vendor/autoload.php';

(new Irc())->prompt();
