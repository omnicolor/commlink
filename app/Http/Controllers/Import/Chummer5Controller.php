<?php

declare(strict_types=1);

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Services\Chummer5\Shadowrun5eConverter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;
use RuntimeException;

use function back;
use function view;

/**
 * @psalm-suppress UnusedClass
 */
class Chummer5Controller extends Controller
{
    public function upload(Request $request): RedirectResponse | View
    {
        try {
            $chummer = new Shadowrun5eConverter($request->character->path());
            $character = $chummer->convert();
            $character->errors = $chummer->getErrors();
            return view(
                'shadowrun5e::character',
                [
                    'character' => $character,
                    'currentStep' => 'review',
                    'errors' => new MessageBag($character->errors),
                    'user' => $request->user(),
                ]
            );
        } catch (RuntimeException $ex) {
            return back()->withInput()->withErrors($ex->getMessage());
        }
    }

    public function view(Request $request): View
    {
        return view('Import.chummer5', ['user' => $request->user()]);
    }
}
