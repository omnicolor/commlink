<?php

require 'vendor/autoload.php';

if (!file_exists(env('DATABASE_URL'))) {
    touch(trim(env('DATABASE_URL')));
    exec('php artisan migrate');
}
