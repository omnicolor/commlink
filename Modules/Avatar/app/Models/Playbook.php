<?php

declare(strict_types=1);

namespace Modules\Avatar\Models;

use RuntimeException;
use Stringable;

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
    /** @var list<string> */
    public array $connections;
    /** @var int<-1, 1> */
    public int $creativity;
    /** @var list<string> */
    public array $demeanor_options;
    public string $description;
    /** @var int<-1, 1> */
    public int $focus;
    /** @var int<-1, 1> */
    public int $harmony;
    /** @var list<string> */
    public array $history;
    public string $moment_of_balance;
    /** @var non-empty-list<string> */
    public array $move_ids;
    public string $name;
    /** @var int<-1, 1> */
    public int $passion;
    public int $page;
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
        $this->creativity = $playbook['creativity'];
        $this->demeanor_options = $playbook['demeanor_options'];
        $this->description = $playbook['description'];
        $this->focus = $playbook['focus'];
        $this->harmony = $playbook['harmony'];
        $this->history = $playbook['history'];
        $this->moment_of_balance = $playbook['moment_of_balance'];
        $this->move_ids = $playbook['moves'];
        $this->name = $playbook['name'];
        $this->page = $playbook['page'];
        $this->passion = $playbook['passion'];
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
