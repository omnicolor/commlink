<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Import;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * @group herolab
 * @medium
 */
final class HeroLabControllerTest extends TestCase
{
    public function testInvalidFileType(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('not-portfolio.por');

        self::actingAs($user)
            ->withHeaders([
                'Referer' => route('import.herolab.view'),
            ])
            ->post(
                route('import.herolab.upload'),
                [
                    'character' => $file,
                ]
            )
            ->assertRedirect(route('import.herolab.view'))
            ->assertSessionHasErrors();
    }

    public function testValidUpload(): void
    {
        $path = explode(
            \DIRECTORY_SEPARATOR,
            dirname(dirname(dirname(dirname(__DIR__))))
        );
        $path[] = 'Data';
        $path[] = 'HeroLab';
        $path[] = 'Shadowrun5e';
        $path[] = 'Test.por';
        $filename = implode(\DIRECTORY_SEPARATOR, $path);

        $file = UploadedFile::fake()->createWithContent(
            'Test.por',
            (string)file_get_contents($filename)
        );

        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(route('import.herolab.upload'), ['character' => $file])
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('7₭');
    }

    public function testView(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->get(route('import.herolab.view'))
            ->assertOk()
            ->assertSee('About Hero Lab');
    }
}
