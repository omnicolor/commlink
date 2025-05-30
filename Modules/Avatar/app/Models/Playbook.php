<?php

declare(strict_types=1);

namespace Modules\Avatar\Models;

use Modules\Avatar\Features\Feature;
use Modules\Avatar\ValueObjects\AttributeModifier;
use Override;
use RuntimeException;
use Stringable;

use function array_keys;
use function assert;
use function config;
use function debug_backtrace;
use function sprintf;
use function strtolower;
use function trigger_error;

use const E_USER_NOTICE;

/**
 * @property-read non-empty-list<Move> $moves
 */
class Playbook implements Stringable
{
    public string $advanced_technique;
    public string $balance_left;
    public string $balance_right;
    /** @var array{0: string, 1: string} */
    public array $connections;
    public AttributeModifier $creativity;
    /** @var list<string> */
    public array $demeanor_options;
    public string $description;
    public Feature $feature;
    public AttributeModifier $focus;
    public string $growth_question;
    public AttributeModifier $harmony;
    /** @var list<string> */
    public array $history;
    public string $moment_of_balance;
    /** @var non-empty-list<string> */
    public array $move_ids;
    public string $name;
    public int $page;
    public AttributeModifier $passion;
    public string $ruleset;

    /** @var ?array<string, mixed> */
    public static ?array $playbooks;

    public function __construct(public string $id)
    {
        $filename = config('avatar.data_path') . 'playbooks.php';
        self::$playbooks ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$playbooks[$id])) {
            throw new RuntimeException(
                sprintf('Playbook ID "%s" is invalid', $id)
            );
        }

        $playbook = self::$playbooks[$id];
        $this->advanced_technique = $playbook['advanced_technique'];
        $this->balance_left = $playbook['balance_left'];
        $this->balance_right = $playbook['balance_right'];
        $this->connections = $playbook['connections'];
        $this->creativity = new AttributeModifier($playbook['creativity']);
        $this->demeanor_options = $playbook['demeanor_options'];
        $this->description = $playbook['description'];
        $feature = new $playbook['feature']([]);
        assert($feature instanceof Feature);
        $this->feature = $feature;
        $this->focus = new AttributeModifier($playbook['focus']);
        $this->growth_question = $playbook['growth_question'];
        $this->harmony = new AttributeModifier($playbook['harmony']);
        $this->history = $playbook['history'];
        $this->moment_of_balance = $playbook['moment_of_balance'];
        $this->move_ids = $playbook['moves'];
        $this->name = $playbook['name'];
        $this->page = $playbook['page'];
        $this->passion = new AttributeModifier($playbook['passion']);
        $this->ruleset = $playbook['ruleset'];
    }

    public function __get(string $name): mixed
    {
        if ('moves' === $name) {
            return $this->getMoves();
        }
        $trace = debug_backtrace();
        trigger_error(
            sprintf(
                'Undefined property via __get(): %s in %s on line %d',
                $name,
                // @phpstan-ignore offsetAccess.notFound
                $trace[0]['file'],
                // @phpstan-ignore offsetAccess.notFound
                $trace[0]['line'],
            ),
            E_USER_NOTICE,
        );
        return null; // @codeCoverageIgnore
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, self>
     */
    public static function all(): array
    {
        $filename = config('avatar.data_path') . 'playbooks.php';
        self::$playbooks ??= require $filename;

        $playbooks = [];
        /** @var string $id */
        foreach (array_keys(self::$playbooks ?? []) as $id) {
            $playbooks[$id] = new self($id);
        }
        return $playbooks;
    }

    /**
     * @param array{0: string, 1: string} $connections
     * @return array{0: string, 1: string}
     */
    public function getConnections(array $connections): array
    {
        return [
            sprintf($this->connections[0], $connections[0] ?? '???'),
            sprintf($this->connections[1], $connections[1] ?? '???'),
        ];
    }

    /**
     * @return non-empty-list<Move>
     */
    protected function getMoves(): array
    {
        $moves = [];
        foreach ($this->move_ids as $move) {
            $moves[] = new Move($move);
        }
        return $moves;
    }
}
