<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Import;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function dirname;
use function explode;
use function file_get_contents;
use function implode;
use function route;

use const DIRECTORY_SEPARATOR;

#[Group('world-anvil')]
#[Medium]
final class WorldAnvilControllerTest extends TestCase
{
    public function testInvalidFileType(): void
    {
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

    public function testUploadWithoutTemplateId(): void
    {
        $file = UploadedFile::fake()->createWithContent(
            'no-template-id.json',
            '{"foo":"bar"}',
        );

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

    public function testUnsupportedSystemUpload(): void
    {
        $file = UploadedFile::fake()->createWithContent(
            'unsupported-system.json',
            '{"templateId":"a"}',
        );

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
        $path = explode(DIRECTORY_SEPARATOR, dirname(__DIR__, 4));
        $path[] = 'Data';
        $path[] = 'WorldAnvil';
        $path[] = 'CyberpunkRed';
        $path[] = 'Caleb.json';
        $filename = implode(DIRECTORY_SEPARATOR, $path);

        $file = UploadedFile::fake()->createWithContent(
            'Caleb.json',
            (string)file_get_contents($filename)
        );

        $user = User::factory()->create();

        self::actingAs($user)
            ->withHeaders(['Referer' => route('import.world-anvil.view')])
            ->post(route('import.world-anvil.upload'), ['character' => $file])
            ->assertRedirect('/characters/cyberpunkred/create/handle');
    }

    public function testValidExpanseUpload(): void
    {
        $path = explode(DIRECTORY_SEPARATOR, dirname(__DIR__, 4));
        $path[] = 'Data';
        $path[] = 'WorldAnvil';
        $path[] = 'Expanse';
        $path[] = 'AricHessel.json';
        $filename = implode(DIRECTORY_SEPARATOR, $path);

        $file = UploadedFile::fake()->createWithContent(
            'AricHessel.json',
            (string)file_get_contents($filename)
        );

        $user = User::factory()->create();

        self::actingAs($user)
            ->withHeaders(['Referer' => route('import.world-anvil.view')])
            ->post(route('import.world-anvil.upload'), ['character' => $file])
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Aric');
    }

    public function testMissingUpload(): void
    {
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
        $user = User::factory()->create();

        self::actingAs($user)
            ->get(route('import.world-anvil.view'))
            ->assertSee('World Anvil');
    }
}
