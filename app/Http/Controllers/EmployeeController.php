<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        return view('Admin.employee');
    }

    public function update() {}

    public function destroy() {}
}
