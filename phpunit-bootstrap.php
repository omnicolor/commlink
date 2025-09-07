<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

if (!file_exists('.env.testing')) {
    throw new RuntimeException(
        'Testing configuration file is missing! Create .env.testing pointing '
            . 'to a testing database',
    );
}
