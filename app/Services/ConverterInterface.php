<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Character;

interface ConverterInterface
{
    /**
     * Convert a file to a character.
     * @return Character
     */
    public function convert(): Character;

    /**
     * Return any errors that happened during conversion.
     * @return array<int, string>
     */
    public function getErrors(): array;
}
