<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(
        Request $request,
        Closure $next
    ): Response
    {
        /*
        |--------------------------------------------------------------------------
        | Only Check Authenticated Users
        |--------------------------------------------------------------------------
        */

        if (Auth::check()) {

            $user = Auth::user();


            /*
            |--------------------------------------------------------------------------
            | Inactive Account
            |--------------------------------------------------------------------------
            */

            if ($user->status !== 'Active') {

                Auth::logout();

                $request->session()->invalidate();

                $request->session()->regenerateToken();


                return redirect()
                    ->route('login')
                    ->withErrors([
                        'email' =>
                            'Your account has been deactivated. Please contact an administrator.',
                    ]);
            }
        }


        return $next($request);
    }
}