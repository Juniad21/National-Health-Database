<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        \App\Services\AuditLogService::logAction(
            action: 'login',
            description: "User {$user->email} logged in successfully",
            module: 'auth',
            severity: 'low'
        );
        
        if ($user->role === 'doctor') {
            return redirect()->intended(route('doctor.dashboard', absolute: false));
        } elseif ($user->role === 'patient') {
            return redirect()->intended(route('patient.dashboard', absolute: false));
        } elseif ($user->role === 'hospital') {
            return redirect()->intended(route('hospital.dashboard', absolute: false));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            \App\Services\AuditLogService::logAction(
                action: 'logout',
                description: "User {$user->email} logged out",
                module: 'auth',
                severity: 'low'
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
