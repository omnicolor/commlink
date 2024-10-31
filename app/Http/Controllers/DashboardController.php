<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use function view;

/**
 * @psalm-suppress UnusedClass
 */
class DashboardController extends Controller
{
    public function show(Request $request): View
    {
        return view('dashboard', ['user' => $request->user()]);
    }
}
