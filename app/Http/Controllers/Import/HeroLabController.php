<?php

declare(strict_types=1);

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\HeroLab\Shadowrun5eConverter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RuntimeException;

use function back;
use function redirect;
use function sprintf;
use function view;

/**
 * @psalm-suppress UnusedClass
 */
class HeroLabController extends Controller
{
    /**
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    public function upload(Request $request): RedirectResponse
    {
        /** @var User */
        $user = Auth::user();
        try {
            $herolab = new Shadowrun5eConverter($request->character->path());
            $character = $herolab->convert();
            $character->errors = $herolab->getErrors();
            $character->owner = $user->email;
            $character->save();
            $request->session()->put('shadowrun5e-partial', $character->id);
            return redirect(sprintf(
                '/characters/%s/create/%s',
                $character->system,
                $character->id,
            ));
        } catch (RuntimeException $ex) {
            return back()->withInput()->withErrors($ex->getMessage());
        }
    }

    public function view(): View
    {
        $user = Auth::user();
        return view('Import.herolab', ['user' => $user]);
    }
}
