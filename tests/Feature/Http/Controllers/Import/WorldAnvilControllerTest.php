<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Import;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

use function dirname;
use function file_get_contents;

use const DIRECTORY_SEPARATOR;

/**
 * Test for World Anvil imports.
 * @group world-anvil
 * @medium
 */
final class WorldAnvilControllerTest extends TestCase
{
    public function testInvalidFileType(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('not-world-anvil-json.jpg');

        self::actingAs($user)
            ->withHeaders([
                'Referer' => route('import.world-anvil.view'),
            ])
            ->post(
                route('import.world-anvil.upload'),
                [
                    'character' => $file,
                ]
            )
            ->assertRedirect(route('import.world-anvil.view'))
            ->assertSessionHasErrors();
    }

    public function testUnsupportedSystemUpload(): void
    {
        $file = UploadedFile::fake()->createWithContent(
            'unsupported-system.json',
            '{"templateId":"a"}',
        );

        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->withHeaders([
                'Referer' => route('import.world-anvil.view'),
            ])
            ->post(
                route('import.world-anvil.upload'),
                [
                    'character' => $file,
                ]
            )
            ->assertRedirect(route('import.world-anvil.view'))
            ->assertSessionHasErrors();
    }

    public function testValidCyberpunkUpload(): void
    {
        $path = explode(
            DIRECTORY_SEPARATOR,
            dirname(dirname(dirname(dirname(__DIR__)))),
        );
        $path[] = 'Data';
        $path[] = 'WorldAnvil';
        $path[] = 'CyberpunkRed';
        $path[] = 'Caleb.json';
        $filename = implode(DIRECTORY_SEPARATOR, $path);

        $file = UploadedFile::fake()->createWithContent(
            'Caleb.json',
            (string)file_get_contents($filename)
        );

        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->withHeaders([
                'Referer' => route('import.world-anvil.view'),
            ])
            ->post(route('import.world-anvil.upload'), ['character' => $file])
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Caleb');
    }

    public function testMissingUpload(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->withHeaders([
                'Referer' => route('import.world-anvil.view'),
            ])
            ->post(route('import.world-anvil.view'))
            ->assertRedirect(route('import.world-anvil.view'))
            ->assertSessionHasErrors();
    }

    public function testView(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->get(route('import.world-anvil.view'))
            ->assertSee('World Anvil');
    }
}
