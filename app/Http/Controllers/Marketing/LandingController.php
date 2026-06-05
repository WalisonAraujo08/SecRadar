<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;

class LandingController extends Controller
{
    public function index()
    {
        return view('marketing.landing');
    }

    public function pricing()
    {
        return view('marketing.landing');
    }
}
