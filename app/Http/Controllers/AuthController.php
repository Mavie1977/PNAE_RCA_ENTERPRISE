<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LoginAudit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\AuditService;
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'citoyen',
            'active' => true,
        ]);

       $user = User::create([
           'name' => $validated['name'],
           'email' => $validated['email'],
           'phone' => $validated['phone'] ?? null,
           'password' => $validated['password'],
           'role' => User::ROLE_CITOYEN,
           'active' => true,
           'ministry_id' => null,
      ]);

        Auth::login($user);

        return redirect()
            ->route('citizen.dashboard')
            ->with('success', 'Compte créé avec succès. Bienvenue sur PNAE-RCA.');
    }

    public function login(Request $request)
{
    $validated = $request->validate([
        'email' => [
            'required',
            'email',
        ],

        'password' => [
            'required',
            'string',
        ],
    ]);

    $email = Str::lower(
        trim($validated['email'])
    );

    $rateLimitKey = sprintf(
        'login:%s|%s',
        $email,
        $request->ip()
    );

    if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
        $seconds = RateLimiter::availableIn($rateLimitKey);

        LoginAudit::create([
            'email' => $email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'event' => 'login_blocked',
            'successful' => false,
            'failure_reason' => 'Trop de tentatives',
            'occurred_at' => now(),
        ]);

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => sprintf(
                    'Trop de tentatives. Réessayez dans %d seconde(s).',
                    $seconds
                ),
            ]);
    }

    $user = User::query()
        ->where('email', $email)
        ->first();

    if (
        ! auth()->attempt(
            [
                'email' => $email,
                'password' => $validated['password'],
                'active' => true,
            ],
            $request->boolean('remember')
        )
    ) {
        RateLimiter::hit(
            $rateLimitKey,
            15 * 60
        );

        LoginAudit::create([
            'user_id' => $user?->id,
            'email' => $email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'event' => 'login_failed',
            'successful' => false,
            'failure_reason' => $user && ! $user->active
                ? 'Compte désactivé'
                : 'Identifiants incorrects',
            'occurred_at' => now(),
        ]);

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' =>
                    'Identifiants incorrects, compte désactivé ou accès refusé.',
            ]);
    }

    RateLimiter::clear($rateLimitKey);

    $request->session()->regenerate();

    LoginAudit::create([
        'user_id' => auth()->id(),
        'email' => auth()->user()->email,
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'event' => 'login_success',
        'successful' => true,
        'occurred_at' => now(),
    ]);

    return $this->redirectAuthenticatedUser();
}

public function redirectDashboard()
{
    return $this->redirectAuthenticatedUser();
}

private function redirectAuthenticatedUser()
{
    return match (auth()->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),

        'responsable' => redirect()->route('responsable.dashboard'),

        'agent' => redirect()->route('agent.dashboard'),

        'citoyen' => redirect()->route('citizen.dashboard'),

        default => redirect()
            ->route('home')
            ->with('warning', 'Rôle utilisateur non reconnu.'),
    };
}
public function logout(Request $request)
{

LoginAudit::create([
    'user_id' => auth()->id(),
    'email' => auth()->user()?->email,
    'ip_address' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'event' => 'logout',
    'successful' => true,
    'occurred_at' => now(),
]);
    auth()->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()
        ->route('home')
        ->with('success', 'Vous êtes déconnecté avec succès.');
}
}