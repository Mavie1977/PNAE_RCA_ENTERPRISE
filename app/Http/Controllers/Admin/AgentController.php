<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\UserManagementService;

class AgentController extends Controller
{
    public function index()
    {
        $agents = User::where('role', 'agent')
            ->latest()
            ->get();

        return view('admin.agents.index', compact('agents'));
    }

    public function create()
    {
        return view('admin.agents.create');
    }


public function dashboard()
    {
        $applications = Application::with(['user', 'procedure', 'documents'])
            ->latest()
            ->get();

        return view('agent.dashboard', [
            'total' => $applications->count(),
            'soumise' => $applications->where('status', 'soumise')->count(),
            'traitement' => $applications->where('status', 'en_traitement')->count(),
            'validee' => $applications->where('status', 'validee')->count(),
            'rejetee' => $applications->where('status', 'rejetee')->count(),
            'applications' => $applications->take(8),
        ]);
    }

    public function applications()
    {
        $applications = Application::with(['user', 'procedure', 'documents'])
            ->latest()
            ->get();

        return view('agent.applications.index', compact('applications'));
    }

    public function show(Application $application)
    {
        $application->load([
            'user',
            'procedure.ministry',
            'documents',
            'workflowLogs.user',
        ]);

        return view('agent.applications.show', compact('application'));
    }
 public function updateStatus(Request $request, Application $application)
    {
        $request->validate([
            'status' => ['required', 'in:soumise,en_traitement,validee,rejetee,terminee'],
            'comment' => ['nullable', 'string'],
        ]);

        $oldStatus = $application->status;

        $application->update([
            'status' => $request->status,
        ]);

        DB::table('workflow_logs')->insert([
            'application_id' => $application->id,
            'user_id' => auth()->id(),
            'from_status' => $oldStatus,
            'to_status' => $request->status,
            'comment' => $request->comment,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('agent.applications.show', $application)
            ->with('success', 'Statut du dossier mis à jour avec succès.');
    }
    public function store(
    Request $request,
    UserManagementService $userManagementService
) {
    $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:150',
        ],

        'email' => [
            'required',
            'email',
            'max:150',
            'unique:users,email',
        ],

        'phone' => [
            'nullable',
            'string',
            'max:30',
        ],

        'password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
        ],

        'role' => [
            'nullable',
            'in:agent,responsable',
        ],
    ]);

    $userManagementService->createAgent($validated);

    return redirect()
        ->route('admin.agents.index')
        ->with(
            'success',
            'L’agent public a été créé avec succès.'
        );
}
    public function toggle(
    User $user,
    UserManagementService $userManagementService
) {
    abort_if(
        ! in_array($user->role, ['agent', 'responsable'], true),
        404
    );

    $userManagementService->toggleStatus($user);

    return back()->with(
        'success',
        'Le statut de l’agent a été modifié avec succès.'
    );
}
}