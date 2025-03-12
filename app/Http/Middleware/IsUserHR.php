<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class IsUserHR
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('employee')->user();
        if($user->getRoleName() !== 'admin')
        {
            /* TODO- HANDLE ALSO THE LOGOUT OF THE SYSTEM WITH HELPER METHODS(TRAITS) */
            return redirect()->route('login');
        }

        return $next($request);
    }
}
