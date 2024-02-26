<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\StarTrekAdventures;

use App\Models\Channel;
use App\Rolls\StarTrekAdventures\Focused;
use Facades\App\Services\DiceService;
use Tests\TestCase;

use const PHP_EOL;

/**
 * Tests for trying a focused test in Star Trek Adventures.
 * @group star-trek-adventures
 * @medium
 */
final class FocusedTest extends TestCase
{
    /**
     * Test making a simple focused roll.
     * @group slack
     * @test
     */
    public function testFocusedSlack(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(20)->andReturn(3);

        /** @var Channel */
        $channel = Channel::factory()->make();
        $response = new Focused('focused 1 2 3', 'username', $channel);
        $response = \json_decode((string)$response->forSlack());
        $response = $response->attachments[0];

        self::assertSame('Rolls: 3 3', $response->footer);
        self::assertSame('Rolled 2 successes', $response->text);
        self::assertSame(
            'username failed a roll with a focus',
            $response->title
        );
    }

    /**
     * Test making a focused roll with extra dice.
     * @group discord
     * @test
     */
    public function testFocusedExtraDice(): void
    {
        DiceService::shouldReceive('rollOne')->times(6)->with(20)->andReturn(3);

        /** @var Channel */
        $channel = Channel::factory()->make();
        $response = (new Focused('focused 1 2 3 4', 'username', $channel))
            ->forDiscord();

        $expected = '**username succeeded with a focus**' . PHP_EOL
            . 'Rolled 6 successes' . PHP_EOL . 'Rolls: 3 3 3 3 3 3';
        self::assertSame($expected, $response);
    }

    /**
     * Test making an focused roll resulting in a complication.
     * @group irc
     * @test
     */
    public function testFocusedWithComplication(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(20)->andReturn(20);

        /** @var Channel */
        $channel = Channel::factory()->make();
        $response = (new Focused('focused 1 2 3', 'username', $channel))
            ->forIrc();

        $expected = 'username failed a roll with a focus' . PHP_EOL
            . 'Rolled 0 successes with 2 complications' . PHP_EOL
            . 'Rolls: 20 20';
        self::assertSame($expected, $response);
    }

    /**
     * Test getting extra successes with natural ones.
     * @group discord
     * @test
     */
    public function testFocusedNaturalOnes(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(20)->andReturn(1);

        /** @var Channel */
        $channel = Channel::factory()->make();
        $response = (new Focused('focused 1 2 3', 'username', $channel))
            ->forDiscord();

        $expected = '**username succeeded with a focus**' . PHP_EOL
            . 'Rolled 4 successes' . PHP_EOL
            . 'Rolls: 1 1';
        self::assertSame($expected, $response);
    }

    /**
     * Test making a focused roll with optional text.
     * @group slack
     * @test
     */
    public function testFocusedRollWithOptionalText(): void
    {
        DiceService::shouldReceive('rollOne')->times(2)->with(20)->andReturn(3);

        /** @var Channel */
        $channel = Channel::factory()->make();
        $response = new Focused(
            'focused 1 2 3 testing',
            'username',
            $channel
        );
        $response = \json_decode((string)$response->forSlack());
        $response = $response->attachments[0];

        self::assertSame('Rolls: 3 3', $response->footer);
        self::assertSame('Rolled 2 successes', $response->text);
        self::assertSame(
            'username failed a roll with a focus for "testing"',
            $response->title
        );
    }
}
