<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class HumanResourceController extends Controller
{
    public function index(): View
    {
        return view('Admin.hrEmploye');
    }
}
