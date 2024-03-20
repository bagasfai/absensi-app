<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckJabatan
{
    public function handle(Request $request, Closure $next, ...$requiredJabatans)
    {
        $user = Auth::user();

        // Check if the user is authenticated
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Check if the user's 'jabatan' matches any of the required 'jabatan'
        foreach ($requiredJabatans as $requiredJabatan) {
            if ($user->jabatan === $requiredJabatan) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized'); // User does not have any of the required 'jabatan'
    }
}
