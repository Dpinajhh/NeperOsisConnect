<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Batasi akses route hanya untuk role tertentu.
     *
     * Contoh: ->middleware('role:admin')
     *         ->middleware('role:admin,guru')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! empty($roles) && ! in_array($user->role, $roles, true)) {
            abort(403, 'Maaf, halaman ini hanya boleh diakses oleh '.implode(' atau ', $roles).'.');
        }

        return $next($request);
    }
}
