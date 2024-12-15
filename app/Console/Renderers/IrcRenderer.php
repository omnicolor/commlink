<?php

declare(strict_types=1);

namespace App\Console\Renderers;

use App\Console\Irc;
use Chewie\Concerns\Aligns;
use Chewie\Concerns\DrawsHotkeys;
use Laravel\Prompts\Themes\Default\Concerns\DrawsBoxes;
use Laravel\Prompts\Themes\Default\Renderer;

class IrcRenderer extends Renderer
{
    use Aligns;
    use DrawsBoxes;
    use DrawsHotkeys;

    private const int PADDING_HORIZONTAL = 2;
    private const int PADDING_VERTICAL = 5;

    public function __invoke(Irc $prompt): string
    {
        $width = $prompt->terminal()->cols() - self::PADDING_HORIZONTAL;
        $height = $prompt->terminal()->lines() - self::PADDING_VERTICAL;

        //$this->center($prompt->message, $width, $height)->each($this->line(...));

        $this->minWidth = 40;
        $this->box(title: 'Testing', body: 'Body', color: 'dim');
        $this->hotkey('Enter', 'Clear', '' !== $prompt->message);
        $this->hotkey('CTRL-C', 'Quit');
        $this->centerHorizontally($this->hotkeys(), $width)
            ->each($this->line(...));
        return (string)$this;
    }
}
