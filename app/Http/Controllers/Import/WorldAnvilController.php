<?php

declare(strict_types=1);

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Services\ConverterInterface;
use App\Services\WorldAnvil\CyberpunkRedConverter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;
use JsonException;

use const JSON_THROW_ON_ERROR;

class WorldAnvilController extends Controller
{
    /**
     * Map of template IDs from World Anvil to Commlink character types.
     * @phpstan-ignore-next-line
     * @psalm-suppress InvalidPropertyAssignmentValue
     * @var array<string, array<string, string>>
     */
    protected array $templateMap = [
        //'3892' => 'ExpanseRPG',
        '6836' => [
            'converter' => CyberpunkRedConverter::class,
            'view' => 'Cyberpunkred.character',
        ],
    ];

    public function upload(Request $request): RedirectResponse | View
    {
        $user = $request->user();
        if (null === $request->character) {
            return back()->withInput()->withErrors('Character is required');
        }
        $characterJson = file_get_contents($request->character->path());
        try {
            $rawCharacter = json_decode(
                json: (string)$characterJson,
                associative: false,
                flags: JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $ex) {
            return back()->withInput()->withErrors($ex->getMessage());
        }
        if (!isset($this->templateMap[$rawCharacter->templateId])) {
            return back()->withInput()->withErrors('Template ID not supported');
        }
        $templateMap = $this->templateMap[$rawCharacter->templateId];
        /** @var ConverterInterface */
        $converter = new $templateMap['converter'](
            $request->character->path()
        );
        $character = $converter->convert();
        // @phpstan-ignore-next-line
        $character->errors = $converter->getErrors();
        return view(
            $templateMap['view'],
            [
                'character' => $character,
                'creating' => true,
                'errors' => new MessageBag($character->errors),
                'user' => $user,
            ],
        );
    }

    public function view(Request $request): View
    {
        $user = $request->user();
        return view('Import.world-anvil', ['user' => $user]);
    }
}
