<?php

namespace App\Http\Controllers\Responsable;

use App\Models\AgentCareerHistory;
use App\Http\Controllers\Controller;
use App\Models\AgentAdvancement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgentController extends Controller
{
    public function index(Request $request): View
    {
        $responsable = $request->user();

        abort_if(
            $responsable->ministry_id === null,
            403,
            'Aucun ministère n’est affecté à ce responsable.'
        );

        $query = User::query()
               ->where('role', 'agent')
               ->with('ministry');

if (! $responsable->isResponsableFonctionPublique()) {
    $query->where(
        'ministry_id',
        $responsable->ministry_id
    );
}

$agents = $query
    ->when(
        $request->filled('search'),
        function ($query) use ($request): void {
            $search = trim(
                (string) $request->input('search')
            );

            $query->where(
                function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('name', 'ilike', "%{$search}%")
                        ->orWhere('email', 'ilike', "%{$search}%")
                        ->orWhere('phone', 'ilike', "%{$search}%");
                }
            );
        }
    )
    ->orderBy('name')
    ->paginate(15)
    ->withQueryString();

        return view(
                    'responsable.agents.index',
                    compact('agents')
               );
    }
	public function show(User $agent): View
{
    abort_unless(
        $agent->role === User::ROLE_AGENT,
        404
    );
/*
    |--------------------------------------------------------------------------
    | Sécurité
    |--------------------------------------------------------------------------
    |
    | Le responsable Fonction publique peut consulter tous les agents.
    | Les autres responsables ne peuvent consulter que les agents
    | de leur propre ministère.
    |
    */
    $agent->load([
        'ministry',
        'agentProfile.ministry',
        'agentProfile.serviceEntity',
        'agentProfile.categoryEntity',
        'agentProfile.gradeEntity',
        'agentProfile.hrFunction',
    ]);

    $histories = AgentCareerHistory::query()
        ->with([
            'fromMinistry',
            'toMinistry',
            'author',
        ])
        ->where('agent_id', $agent->id)
        ->latest('effective_at')
        ->latest('id')
        ->get();

    $advancements = AgentAdvancement::query()
        ->with([
            'oldCategory',
            'newCategory',
            'oldGrade',
            'newGrade',
            'oldFunction',
            'newFunction',
            'author',
        ])
        ->where('agent_id', $agent->id)
        ->orderByDesc('effective_at')
        ->orderByDesc('id')
        ->get();

    $leaveStats = [
    'total' => $agent->leaves()->count(),

    'pending' => $agent->leaves()
        ->where('status', 'pending')
        ->count(),

    'approved' => $agent->leaves()
        ->where('status', 'approved')
        ->count(),

    'rejected' => $agent->leaves()
        ->where('status', 'rejected')
        ->count(),

    'days_approved' => $agent->leaves()
        ->where('status', 'approved')
        ->sum('days_count'),
	
	];
	
		$disciplinaryStats = [
    'total' => $agent->disciplinaryActions()->count(),

    'pending' => $agent->disciplinaryActions()
        ->where('status', 'pending')
        ->count(),

    'approved' => $agent->disciplinaryActions()
        ->where('status', 'approved')
        ->count(),

    'rejected' => $agent->disciplinaryActions()
        ->where('status', 'rejected')
        ->count(),

];

$documentStats = [
    'total' => $agent->rhDocuments()->count(),

    'active' => $agent->rhDocuments()
        ->where('active', true)
        ->count(),

    'archived' => $agent->rhDocuments()
        ->where('active', false)
        ->count(),

    'latest' => $agent->rhDocuments()
        ->orderByDesc('document_date')
        ->orderByDesc('id')
        ->first(),
];

$trainingStats = [
    'total' => $agent->trainings()->count(),

    'planned' => $agent->trainings()
        ->where('status', 'planned')
        ->count(),

    'in_progress' => $agent->trainings()
        ->where('status', 'in_progress')
        ->count(),

    'completed' => $agent->trainings()
        ->where('status', 'completed')
        ->count(),

    'skills' => $agent->skills()->count(),

    'certified' => $agent->trainings()
        ->whereNotNull('certificate_reference')
        ->count(),
];
    return view(
    'responsable.agents.show',
    compact(
        'agent',
        'histories',
        'advancements',
        'leaveStats',
        'disciplinaryStats',
        'documentStats',
		'trainingStats'
    )
);
}
}