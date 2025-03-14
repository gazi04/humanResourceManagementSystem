<?php

namespace App\Http\Middleware;

use App\Models\Employee;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!Auth::guard('employee')->check()){
            /** @var Employee $employee */
            $employee = Auth::guard('employee')->user();
            if($employee->getRoleName() == 'admin'){
                /* TODO- REDIRECT THE USERS TO THE CORRESPONDING PAGE BASED ON THEIR ROLES */
                dd('admin');
            }

            if($employee->getRoleName() == 'manager'){
                dd('admin');
            }

            if($employee->getRoleName() == 'hr'){
                dd('admin');
            }

            if($employee->getRoleName() == 'employee'){
                dd('admin');
            }

        }

        return $next($request);
    }
}
