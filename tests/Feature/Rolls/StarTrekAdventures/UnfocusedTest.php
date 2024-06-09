<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\StarTrekAdventures;

use App\Models\Channel;
use App\Rolls\StarTrekAdventures\Unfocused;
use Facades\App\Services\DiceService;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function json_decode;

use const PHP_EOL;

#[Group('star-trek-adventures')]
#[Medium]
final class UnfocusedTest extends TestCase
{
    #[Group('slack')]
    public function testUnfocusedSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();

        DiceService::shouldReceive('rollOne')->times(2)->with(20)->andReturn(3);

        $response = new Unfocused('unfocused 1 2 3', 'username', $channel);
        $response = json_decode((string)$response->forSlack());
        $response = $response->attachments[0];

        self::assertSame('Rolls: 3 3', $response->footer);
        self::assertSame('Rolled 2 successes', $response->text);
        self::assertSame(
            'username failed a roll without a focus',
            $response->title
        );
    }

    /**
     * Test making an unfocused roll with extra dice.
     */
    #[Group('discord')]
    public function testUnfocusedExtraDice(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();

        DiceService::shouldReceive('rollOne')->times(6)->with(20)->andReturn(3);

        $response = (new Unfocused('unfocused 1 2 3 4', 'username', $channel))
            ->forDiscord();

        $expected = '**username succeeded without a focus**' . PHP_EOL
            . 'Rolled 6 successes' . PHP_EOL . 'Rolls: 3 3 3 3 3 3';
        self::assertSame($expected, $response);
    }

    /**
     * Test making an unfocused roll resulting in a complication.
     */
    #[Group('discord')]
    public function testUnfocusedWithComplication(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();

        DiceService::shouldReceive('rollOne')
            ->times(2)
            ->with(20)
            ->andReturn(20);

        $response = (new Unfocused('unfocused 1 2 3', 'username', $channel))
            ->forDiscord();

        $expected = '**username failed a roll without a focus**' . PHP_EOL
            . 'Rolled 0 successes with 2 complications' . PHP_EOL
            . 'Rolls: 20 20';
        self::assertSame($expected, $response);
    }

    /**
     * Test getting extra successes with natural ones.
     */
    #[Group('discord')]
    public function testUnfocusedNaturalOnes(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(20)->andReturn(1);

        /** @var Channel */
        $channel = Channel::factory()->make();

        $response = (new Unfocused('unfocused 1 2 3', 'username', $channel))
            ->forDiscord();

        $expected = '**username succeeded without a focus**' . PHP_EOL
            . 'Rolled 4 successes' . PHP_EOL
            . 'Rolls: 1 1';
        self::assertSame($expected, $response);
    }

    /**
     * Test making an unfocused roll with optional text.
     */
    #[Group('slack')]
    public function testUnfocusedRollWithOptionalText(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(20)->andReturn(3);

        /** @var Channel */
        $channel = Channel::factory()->make();

        $response = new Unfocused(
            'unfocused 1 2 3 testing',
            'username',
            $channel
        );
        $response = json_decode((string)$response->forSlack());
        $response = $response->attachments[0];

        self::assertSame('Rolls: 3 3', $response->footer);
        self::assertSame('Rolled 2 successes', $response->text);
        self::assertSame(
            'username failed a roll without a focus for "testing"',
            $response->title
        );
    }

    #[Group('irc')]
    public function testUnfocusedIrc(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(20)->andReturn(1);

        /** @var Channel */
        $channel = Channel::factory()->make();

        $response = (new Unfocused('unfocused 1 2 3', 'username', $channel))
            ->forIrc();

        $expected = 'username succeeded without a focus' . PHP_EOL
            . 'Rolled 4 successes' . PHP_EOL
            . 'Rolls: 1 1';
        self::assertSame($expected, $response);
    }
}
