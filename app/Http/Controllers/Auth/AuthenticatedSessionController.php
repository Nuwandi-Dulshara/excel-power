<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (! Auth::attempt([
            $loginType => $request->login,
            'password' => $request->password,
        ], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'login' => 'The provided credentials are incorrect.',
            ]);
        }

        $request->session()->regenerate();

        return $this->redirectToDashboard(Auth::user());
    }

    private function redirectToDashboard(User $user): RedirectResponse
    {
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('cashier')) {
            return redirect()->route('admin.sales.index');
        }

        if ($user->hasRole('developer')) {
            return redirect()->route('developer.dashboard');
        }

        return redirect()->route('login');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
