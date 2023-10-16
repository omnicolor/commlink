<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls;

use App\Models\Channel;
use App\Rolls\Generic;
use Facades\App\Services\DiceService;
use Tests\TestCase;

/**
 * Tests for rolling generic dice.
 * @group discord
 * @group slack
 * @medium
 */
final class GenericTest extends TestCase
{
    /**
     * Test a simple roll with no addition or subtraction.
     * @test
     */
    public function testSimple(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([2, 2, 2]);

        /** @var Channel */
        $channel = Channel::factory()->make();
        $response = new Generic('3d6', 'username', $channel);
        $response = \json_decode((string)$response->forSlack());
        self::assertSame('Rolls: 2, 2, 2', $response->attachments[0]->footer);
        self::assertSame(
            'Rolling: 3d6 = [6] = 6',
            $response->attachments[0]->text
        );
    }

    /**
     * Test a simple roll with a description.
     * @test
     */
    public function testWithDescription(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(4, 6)
            ->andReturn([3, 3, 3, 3]);

        /** @var Channel */
        $channel = Channel::factory()->make();
        $roll = new Generic('4d6 testing', 'user', $channel);
        $response = \json_decode((string)$roll->forSlack());
        self::assertSame(
            'Rolls: 3, 3, 3, 3',
            $response->attachments[0]->footer
        );
        self::assertSame(
            'Rolling: 4d6 = [12] = 12',
            $response->attachments[0]->text
        );
        self::assertSame(
            'user rolled 12 for "testing"',
            $response->attachments[0]->title
        );

        $expected = '**user rolled 12 for "testing"**' . \PHP_EOL
            . 'Rolling: 4d6 = [12] = 12' . \PHP_EOL
            . '_Rolls: 3, 3, 3, 3_';
        $discord = $roll->forDiscord();
        self::assertSame($expected, $discord);
    }

    /**
     * Test a more complex calculation.
     * @test
     */
    public function testWithCalculation(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(2, 10)
            ->andReturn([10, 10]);

        /** @var Channel */
        $channel = Channel::factory()->make();
        $roll = new Generic('4+2d10-1*10 foo', 'Bob', $channel);
        $response = \json_decode((string)$roll->forSlack());
        self::assertSame('Rolls: 10, 10', $response->attachments[0]->footer);
        self::assertSame(
            'Rolling: 4+2d10-1*10 = 4+[20]-1*10 = 14',
            $response->attachments[0]->text
        );

        $expected = '**Bob rolled 14 for "foo"**' . \PHP_EOL
            . 'Rolling: 4+2d10-1*10 = 4+[20]-1*10 = 14' . \PHP_EOL
            . '_Rolls: 10, 10_';
        $discord = $roll->forDiscord();
        self::assertSame($expected, $discord);
    }
}
