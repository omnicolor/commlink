<?php

declare(strict_types=1);

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Services\HeroLab\Shadowrun5eConverter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;
use RuntimeException;

class HeroLabController extends Controller
{
    public function upload(Request $request): RedirectResponse | View
    {
        $user = \Auth::user();
        try {
            $herolab = new Shadowrun5eConverter($request->character->path());
            $character = $herolab->convert();
            $character->errors = $herolab->getErrors();
            return view(
                'Shadowrun5e.character',
                [
                    'character' => $character,
                    'errors' => new MessageBag($character->errors),
                    'user' => $user,
                ]
            );
        } catch (RuntimeException $ex) {
            return back()->withInput()->withErrors($ex->getMessage());
        }
    }

    public function view(): View
    {
        $user = \Auth::user();
        return view('Import.herolab', ['user' => $user]);
    }
}
