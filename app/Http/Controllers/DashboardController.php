<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Character;

class DashboardController extends Controller
{
    /**
     * Show dashboard
     *
     * @return \Illuminate\View\View
     */
    public function show(): \Illuminate\View\View
    {
        $characters = Character::where('owner', \Auth::user()->email)->get();

        return view('dashboard', ['characters' => $characters]);
    }
}
