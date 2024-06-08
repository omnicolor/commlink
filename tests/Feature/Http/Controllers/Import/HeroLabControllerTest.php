<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Import;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function dirname;
use function file_get_contents;

use const DIRECTORY_SEPARATOR;

/**
 * @group herolab
 */
#[Medium]
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
            DIRECTORY_SEPARATOR,
            dirname(dirname(dirname(dirname(__DIR__))))
        );
        $path[] = 'Data';
        $path[] = 'HeroLab';
        $path[] = 'Shadowrun5e';
        $path[] = 'valid-portfolio2.por';
        $filename = implode(DIRECTORY_SEPARATOR, $path);

        $file = UploadedFile::fake()->createWithContent(
            'valid-portfolio2.por',
            (string)file_get_contents($filename)
        );

        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(route('import.herolab.upload'), ['character' => $file])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
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
