<?php

declare(strict_types=1);

namespace App\Http\Controllers\Shadowrun5e;

use App\Http\Controllers\Controller;
use App\Models\Shadowrun5e\Character;
use Illuminate\Http\Request;

class ContactsController extends Controller
{
    public function store(Character $character, Request $request)
    {
        return 'Hello world';
    }

    public function index(Character $character, Request $request)
    {
        return $character->contacts;
    }
}
