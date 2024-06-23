<?php

declare(strict_types=1);

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ConverterInterface;
use App\Services\WorldAnvil\CyberpunkRedConverter;
use App\Services\WorldAnvil\ExpanseConverter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;
use JsonException;

use function array_key_exists;
use function back;
use function file_get_contents;
use function json_decode;
use function view;

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
        ExpanseConverter::TEMPLATE_ID => [
            'converter' => ExpanseConverter::class,
            'view' => 'Expanse.character',
        ],
        CyberpunkRedConverter::TEMPLATE_ID => [
            'converter' => CyberpunkRedConverter::class,
            'redirect' => '/characters/cyberpunkred/create/handle',
            'session' => 'cyberpunkredpartial',
        ],
    ];

    public function upload(Request $request): RedirectResponse | View
    {
        /** @var User */
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
            return back()->withInput()
                ->withErrors('System is not (yet) supported.');
        }
        $templateMap = $this->templateMap[$rawCharacter->templateId];
        /** @var ConverterInterface */
        $converter = new $templateMap['converter']($request->character->path());
        $character = $converter->convert();
        // @phpstan-ignore-next-line
        $character->errors = $converter->getErrors();
        $character->owner = $user->email;
        $character->save();
        $character->refresh();
        if (array_key_exists('redirect', $templateMap)) {
            session([$templateMap['session'] => $character->id]);
            return new RedirectResponse($templateMap['redirect']);
        }
        return view(
            $templateMap['view'],
            [
                'character' => $character,
                'creating' => true,
                // @phpstan-ignore-next-line
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
