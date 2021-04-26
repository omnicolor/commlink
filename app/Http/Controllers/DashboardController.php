<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    /**
     * Show dashboard.
     * @return \Illuminate\View\View
     */
    public function show(): \Illuminate\View\View
    {
        return view('dashboard', ['user' => \Auth::user()]);
    }
}
