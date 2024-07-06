<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Import;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('chummer5')]
#[Group('shadowrun5e')]
#[Medium]
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
            dirname(__DIR__, 4)
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
