<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

/**
 * @psalm-suppress UnusedClass
 */
class TrustHosts extends Middleware
{
    /**
     * Get the host patterns that should be trusted.
     * @return array<int, ?string>
     */
    public function hosts(): array
    {
        return [
            $this->allSubdomainsOfApplicationUrl(),
        ];
    }
}
