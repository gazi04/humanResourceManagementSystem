<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ManagerController extends Controller
{
    public function index(): View
    {
        return view('Admin.depMenager');
    }
}
