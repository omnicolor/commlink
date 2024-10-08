<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Renderers\IrcRenderer;
use Chewie\Concerns\RegistersThemes;
use Chewie\Input\KeyPressListener;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;

class Irc extends Prompt
{
    use RegistersThemes;

    public string $message = '';

    public string $server = 'irc.freenode.net';
    public string $port = '6667';
    public string $nickname = 'commlink';
    /** @var array<int, string> */
    public array $channels = ['#commlink', '#shadowrun'];

    public function __construct()
    {
        $this->registerTheme(IrcRenderer::class);

        $valid = array_merge(
            range('a', 'z'),
            range('A', 'Z'),
            [' ', '.', ',']
        );
        KeyPressListener::for($this)
            ->on($valid, function (string $key) {
                $this->message .= $key;
            })
            ->on(Key::ENTER, function () {
                $this->message = '';
            })
            ->on(Key::BACKSPACE, function () {
                $this->message = substr($this->message, 0, -1);
            })
            ->on(Key::CTRL_C, function () {
                $this->terminal()->exit();
            })
            ->listen();
    }

    public function value(): mixed
    {
        return null;
    }
}
