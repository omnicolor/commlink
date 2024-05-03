<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Import;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * Tests for Chummer5 imports.
 * @group chummer5
 * @medium
 */
final class Chummer5ControllerTest extends TestCase
{
    public function testInvalidFileType(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('not-chummer.jpg');

        self::actingAs($user)
            ->withHeaders([
                'Referer' => route('import.chummer5.view'),
            ])
            ->post(route('import.chummer5.upload'), ['character' => $file])
            ->assertRedirect(route('import.chummer5.view'))
            ->assertSessionHasErrors();
    }

    public function testValidUpload(): void
    {
        $path = explode(
            \DIRECTORY_SEPARATOR,
            dirname(dirname(dirname(dirname(__DIR__))))
        );
        $path[] = 'Data';
        $path[] = 'Chummer5';
        $path[] = 'birdman.chum5';
        $filename = implode(\DIRECTORY_SEPARATOR, $path);

        $file = UploadedFile::fake()->createWithContent(
            'birdman.chum5',
            (string)file_get_contents($filename)
        );

        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(route('import.chummer5.upload'), ['character' => $file])
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('BirdMan');
    }

    public function testView(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->get(route('import.chummer5.view'))
            ->assertOk()
            ->assertSee('Import - Chummer 5');
    }
}
