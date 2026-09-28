<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;

class CitizenController extends Controller
{
    public function index()
    {
        $citizens = User::where('role', 'citoyen')
            ->withCount('applications')
            ->latest()
            ->get();

        return view('admin.citizens.index', compact('citizens'));
    }

    public function show(User $user)
    {
        abort_if($user->role !== 'citoyen', 404);

        $user->load([
            'applications.procedure',
            'applications.documents',
        ]);

        return view('admin.citizens.show', compact('user'));
    }

    public function toggle(User $user)
    {
        abort_if($user->role !== 'citoyen', 404);

        $user->update([
            'active' => ! $user->active,
        ]);

        AuditService::log(
            'Changement de statut',
            'Citoyen',
            $user->id,
            [
                'name' => $user->name,
                'active' => $user->active,
            ]
        );

        return back()->with(
            'success',
            'Statut du citoyen modifié avec succès.'
        );
    }
}