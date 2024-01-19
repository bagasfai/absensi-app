<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckJabatan
{
    public function handle(Request $request, Closure $next, $requiredJabatan)
    {
        $user = Auth::user();

        if ($user && $user->jabatan === $requiredJabatan) {
            return $next($request);
        }

        abort(403, 'Unauthorized'); // You can customize the response as needed
    }
}
