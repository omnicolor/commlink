<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Irc;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'app:test-command';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        (new Irc())->prompt();
        return self::SUCCESS;
    }
}
