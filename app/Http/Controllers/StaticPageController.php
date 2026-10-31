<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class StaticPageController extends Controller
{
    public function impressum(): Response
    {
        return Inertia::render('Static/Impressum');
    }

    public function datenschutz(): Response
    {
        return Inertia::render('Static/Datenschutz');
    }

    public function regeln(): Response
    {
        return Inertia::render('Static/Regeln');
    }
}
