<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized access.');
        }

        if ($user->status !== 'active') {
            auth()->logout();

            return redirect()
                ->route('login')
                ->with('error', 'Your account is inactive. Please contact the administrator.');
        }

        $activeRoleExists = $user->roles()
            ->where('status', 'active')
            ->exists();

        if (!$activeRoleExists) {
            auth()->logout();

            return redirect()
                ->route('login')
                ->with('error', 'Your assigned role is inactive. Please contact the administrator.');
        }

        return $next($request);
    }
}