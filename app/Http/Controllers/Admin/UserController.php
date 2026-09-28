<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Ministry;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $authenticatedUser = $request->user();

        $users = User::query()
            ->with('ministry')
            ->when(
                $authenticatedUser->isResponsable(),
                fn ($query) => $query
                    ->where('role', User::ROLE_AGENT)
                    ->where(
                        'ministry_id',
                        $authenticatedUser->ministry_id
                    )
            )
            ->when(
                $request->filled('q'),
                function ($query) use ($request): void {
                    $search = trim((string) $request->query('q'));

                    $query->where(function ($subQuery) use ($search): void {
                        $subQuery
                            ->where('name', 'ilike', "%{$search}%")
                            ->orWhere('email', 'ilike', "%{$search}%")
                            ->orWhere('phone', 'ilike', "%{$search}%");
                    });
                }
            )
            ->when(
                $request->filled('role')
                    && $authenticatedUser->isAdmin(),
                fn ($query) => $query->where(
                    'role',
                    $request->query('role')
                )
            )
            ->when(
                $request->filled('active'),
                fn ($query) => $query->where(
                    'active',
                    $request->boolean('active')
                )
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => User::roles(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', User::class);

        $authenticatedUser = $request->user();

        $ministries = $authenticatedUser->isAdmin()
            ? Ministry::query()->orderBy('name')->get()
            : Ministry::query()
                ->whereKey($authenticatedUser->ministry_id)
                ->get();

        $roles = $authenticatedUser->isAdmin()
            ? User::roles()
            : [
                User::ROLE_AGENT => 'Agent public',
            ];

        return view(
            'admin.users.create',
            compact('ministries', 'roles')
        );
    }

    public function store(
        StoreUserRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        if ($request->user()->isResponsable()) {
            $data['role'] = User::ROLE_AGENT;
            $data['ministry_id'] = $request->user()->ministry_id;
        }

        if (
            ! in_array(
                $data['role'],
                [User::ROLE_AGENT, User::ROLE_RESPONSABLE],
                true
            )
        ) {
            $data['ministry_id'] = null;
        }

        $data['active'] = $request->boolean('active', true);

        $user = DB::transaction(
            fn () => User::create($data)
        );

        AuditService::log(
            'Création utilisateur',
            'Utilisateur',
            $user->id,
            [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'ministry_id' => $user->ministry_id,
            ]
        );

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'Le compte utilisateur a été créé avec succès.'
            );
    }

    public function show(User $user): View
    {
        $this->authorize('view', $user);

        $user->load('ministry');

        return view('admin.users.show', compact('user'));
    }

    public function edit(
        Request $request,
        User $user
    ): View {
        $this->authorize('update', $user);

        $authenticatedUser = $request->user();

        $ministries = $authenticatedUser->isAdmin()
            ? Ministry::query()->orderBy('name')->get()
            : Ministry::query()
                ->whereKey($authenticatedUser->ministry_id)
                ->get();

        $roles = $authenticatedUser->isAdmin()
            ? User::roles()
            : [
                User::ROLE_AGENT => 'Agent public',
            ];

        return view(
            'admin.users.edit',
            compact('user', 'ministries', 'roles')
        );
    }

    public function update(
        UpdateUserRequest $request,
        User $user
    ): RedirectResponse {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        if ($request->user()->isResponsable()) {
            $data['role'] = User::ROLE_AGENT;
            $data['ministry_id'] = $request->user()->ministry_id;
        }

        if (
            ! in_array(
                $data['role'],
                [User::ROLE_AGENT, User::ROLE_RESPONSABLE],
                true
            )
        ) {
            $data['ministry_id'] = null;
        }

        $data['active'] = $request->boolean('active');

        $oldValues = $user->only([
            'name',
            'email',
            'phone',
            'role',
            'active',
            'ministry_id',
        ]);

        $user->update($data);

        AuditService::log(
            'Modification utilisateur',
            'Utilisateur',
            $user->id,
            [
                'before' => $oldValues,
                'after' => $user->only([
                    'name',
                    'email',
                    'phone',
                    'role',
                    'active',
                    'ministry_id',
                ]),
            ]
        );

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'Le compte utilisateur a été mis à jour.'
            );
    }

    public function toggle(
        Request $request,
        User $user
    ): RedirectResponse {
        $this->authorize('toggle', $user);

        if ($user->is($request->user())) {
            return back()->withErrors([
                'user' =>
                    'Vous ne pouvez pas désactiver votre propre compte.',
            ]);
        }

        if (
            $user->isAdmin()
            && $user->active
            && User::where('role', User::ROLE_ADMIN)
                ->where('active', true)
                ->count() <= 1
        ) {
            return back()->withErrors([
                'user' =>
                    'Le dernier administrateur actif ne peut pas être désactivé.',
            ]);
        }

        $user->update([
            'active' => ! $user->active,
        ]);

        AuditService::log(
            $user->active
                ? 'Activation utilisateur'
                : 'Désactivation utilisateur',
            'Utilisateur',
            $user->id,
            [
                'active' => $user->active,
            ]
        );

        return back()->with(
            'success',
            $user->active
                ? 'Le compte a été activé.'
                : 'Le compte a été désactivé.'
        );
    }

    public function resetPassword(
        Request $request,
        User $user
    ): RedirectResponse {
        $this->authorize('resetPassword', $user);

        $temporaryPassword = Str::password(
            length: 12,
            letters: true,
            numbers: true,
            symbols: false
        );

        $user->update([
            'password' => $temporaryPassword,
        ]);

        AuditService::log(
            'Réinitialisation mot de passe',
            'Utilisateur',
            $user->id,
            [
                'email' => $user->email,
            ]
        );

        return back()
            ->with('success', 'Le mot de passe a été réinitialisé.')
            ->with('temporary_password', $temporaryPassword);
    }

    public function destroy(
        Request $request,
        User $user
    ): RedirectResponse {
        $this->authorize('delete', $user);

        if ($user->is($request->user())) {
            return back()->withErrors([
                'user' =>
                    'Vous ne pouvez pas supprimer votre propre compte.',
            ]);
        }

        $hasHistory = $user->applications()->exists()
            || $user->payments()->exists();

        if ($hasHistory) {
            return back()->withErrors([
                'user' =>
                    'Ce compte possède un historique. Désactivez-le au lieu de le supprimer.',
            ]);
        }

        $userData = $user->only([
            'id',
            'name',
            'email',
            'role',
        ]);

        $user->delete();

        AuditService::log(
            'Suppression utilisateur',
            'Utilisateur',
            $userData['id'],
            $userData
        );

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'Le compte utilisateur a été supprimé.'
            );
    }
}