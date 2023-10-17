<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show dashboard.
     * @return View
     */
    public function show(): View
    {
        return view('dashboard', ['user' => Auth::user()]);
    }
}
