<?php

declare(strict_types=1);

namespace Modules\Subversion\Models;

use Override;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;

class Impulse implements Stringable
{
    public string $description;
    public ImpulseDowntime $downtime;
    public string $name;
    public int $page;
    public string $ruleset;
    public string $triggers;

    /**
     * @var array<string, ImpulseResponse>
     */
    public array $responses = [];

    /**
     * @var array<string, array<string, int|string>>
     */
    public static ?array $impulses;

    public function __construct(public string $id)
    {
        $filename = config('subversion.data_path') . 'impulses.php';
        self::$impulses ??= require $filename;

        if (!isset(self::$impulses[$id])) {
            throw new RuntimeException(sprintf('Impulse "%s" not found', $id));
        }

        $impulse = self::$impulses[$id];
        $this->description = $impulse['description'];
        $this->downtime = new ImpulseDowntime(
            $impulse['downtime']['name'],
            $impulse['downtime']['description'],
            $impulse['downtime']['effects'],
        );
        $this->name = $impulse['name'];
        $this->page = $impulse['page'];
        /** @var string $responseId */
        foreach ($impulse['responses'] as $responseId => $response) {
            $this->responses[$responseId] = new ImpulseResponse(
                $responseId,
                $response['name'],
                $response['description'],
                $response['effects'],
            );
        }
        $this->ruleset = $impulse['ruleset'];
        $this->triggers = $impulse['triggers'];
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, Impulse>
     */
    public static function all(): array
    {
        $filename = config('subversion.data_path') . 'impulses.php';
        self::$impulses ??= require $filename;

        $impulses = [];
        /** @var string $id */
        foreach (self::$impulses ?? [] as $id => $impulse) {
            $impulses[$id] = new Impulse($impulse['id']);
        }
        return $impulses;
    }
}
