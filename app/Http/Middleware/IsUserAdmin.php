<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\AuthHelper;

class IsUserAdmin
{
    use AuthHelper;
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
            return $this->logoutUser($request);
        }

        return $next($request);
    }
}
