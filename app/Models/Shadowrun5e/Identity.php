<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Class representing a Shadowrun identity.
 * @psalm-suppress PossiblyUnusedProperty
 */
class Identity
{
    /**
     * Identifier for the identity.
     */
    public int $identifier;

    /**
     * Collection of fake licenses.
     * @var array<int, License>
     */
    public array $licenses = [];

    /**
     * Collection of lifestyles.
     * @var array<int, Lifestyle>
     */
    public array $lifestyles = [];

    /**
     * Name for the identity.
     */
    public string $name;

    /**
     * Optional notes about the identity, its licenses, its SIN, and/or its
     * lifestyles.
     */
    public ?string $notes;

    /**
     * If the Identity is for a SINner quality, denotes what level of SINner.
     */
    public ?string $sinner = null;

    /**
     * If the identity has a fake SIN (not from a SINner quality) what level
     * the fake is.
     */
    public ?int $sin = null;

    /**
     * Collection of the identity's subscriptions.
     * @var array<int, array<string, int|string>>
     */
    public array $subscriptions = [];

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Create a new identity from a Mongo blob.
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): Identity
    {
        $identity = new self();
        $identity->identifier = (int)$raw['id'];
        $identity->name = $raw['name'];
        $identity->notes = $raw['notes'] ?? '';

        foreach ($raw['licenses'] ?? [] as $license) {
            $identity->licenses[] = new License(
                $license['rating'],
                $license['license'],
            );
        }

        foreach ($raw['lifestyles'] ?? [] as $rawLifestyle) {
            $lifestyle = new Lifestyle($rawLifestyle['name']);
            foreach ($rawLifestyle['options'] ?? [] as $option) {
                try {
                    $lifestyle->options[] = new LifestyleOption($option);
                } catch (\RuntimeException $ex) {
                    if ('' !== $identity->notes) {
                        $identity->notes .= \PHP_EOL;
                    }
                    $identity->notes .= \sprintf(
                        'Option "%s" was not found for lifestyle "%s"',
                        $option,
                        $rawLifestyle['name']
                    );
                }
            }
            $lifestyle->quantity = $rawLifestyle['quantity'];
            $identity->lifestyles[] = $lifestyle;
        }

        if (isset($raw['sinner'])) {
            $identity->sin = null;
            $identity->sinner = $raw['sin'];
        } elseif (isset($raw['sin'])) {
            $identity->sin = (int)$raw['sin'];
            $identity->sinner = null;
        }
        return $identity;
    }

    /**
     * Return the cost of the identity including licenses, lifestyles, and SINs.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getCost(): int
    {
        $cost = 0;
        if (isset($this->sin)) {
            $cost += $this->sin * 2500;
        }
        foreach ($this->licenses as $license) {
            $cost += $license->getCost();
        }
        foreach ($this->lifestyles as $lifestyle) {
            $cost += $lifestyle->getCost();
        }
        return $cost;
    }
}
