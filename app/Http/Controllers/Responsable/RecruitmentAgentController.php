<?php

namespace App\Http\Controllers\Responsable;

use App\Http\Controllers\Controller;


use App\Models\Ministry;
use App\Models\User;
use App\Models\Service;
use App\Models\HrCategory;
use App\Models\HrGrade;
use App\Models\HrFunction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Models\AgentCareerHistory;
use App\Services\HumanResources\AgentCareerService;
use Illuminate\Support\Facades\DB;
use App\Models\AgentProfile;
use App\Services\HumanResources\AgentMatriculeService;


class RecruitmentAgentController extends Controller
{
    public function __construct(
    private readonly AgentCareerService $careerService
) {
}
    public function create(Request $request): View
    {
        $ministries = Ministry::query()
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return view(
            'responsable.recruitment.agents.create',
            compact('ministries')
        );
    }

    public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'name' => [
            'required',
            'string',
            'max:255',
        ],

        'email' => [
            'required',
            'email',
            'max:255',
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

        'ministry_id' => [
            'required',
            'integer',
            Rule::exists('ministries', 'id')
                ->where('active', true),
        ],

        'recruitment_reference' => [
            'nullable',
            'string',
            'max:100',
        ],

        'recruitment_reason' => [
            'required',
            'string',
            'max:2000',
        ],
    ]);

    $agent = $this->careerService->recruit(
        [
            'name' =>
                $validated['name'],

            'email' =>
                $validated['email'],

            'phone' =>
                $validated['phone'] ?? null,

            'password' =>
                Hash::make($validated['password']),

            'ministry_id' =>
                (int) $validated['ministry_id'],

            'recruitment_date' =>
                now()->toDateString(),

            'appointment_date' =>
                now()->toDateString(),
        ],

        $request->user(),

        $validated['recruitment_reason'],

        $validated['recruitment_reference'] ?? null
    );

    return redirect()
        ->route('responsable.agents.index')
        ->with(
            'success',
            "Le compte agent {$agent->name} a été créé avec le matricule {$agent->agentProfile->matricule}, affecté et inscrit dans l’historique."
        );
}

public function transferForm(User $agent): View
{
    abort_unless(
        $agent->role === 'agent',
        404
    );

    $agent->load('ministry');

    $ministries = Ministry::query()
        ->where('active', true)
        ->whereKeyNot($agent->ministry_id)
        ->orderBy('name')
        ->get();

    return view(
        'responsable.recruitment.agents.transfer',
        compact(
            'agent',
            'ministries'
        )
    );
}
public function transfer(
    Request $request,
    User $agent
): RedirectResponse {
    $validated = $request->validate([
        'ministry_id' => [
            'required',
            'integer',
            Rule::exists('ministries', 'id')
                ->where('active', true),
            Rule::notIn([
                $agent->ministry_id,
            ]),
        ],
        'reason' => [
            'required',
            'string',
            'max:2000',
        ],
        'reference' => [
            'nullable',
            'string',
            'max:100',
        ],
        'effective_at' => [
            'nullable',
            'date',
        ],
    ]);

    $this->careerService->transfer(
        $agent,
        (int) $validated['ministry_id'],
        $request->user(),
        $validated['reason'],
        $validated['reference'] ?? null,
        $validated['effective_at'] ?? null
    );

    return redirect()
        ->route('responsable.agents.index')
        ->with(
            'success',
            'La mutation de l’agent a été enregistrée.'
        );
}
   
   
    public function edit(User $agent): View
{
    abort_unless(
        $agent->role === User::ROLE_AGENT,
        404
    );

    $agent->load([
    'ministry',
    'agentProfile',
    'agentProfile.serviceEntity',
    'agentProfile.categoryEntity',
    'agentProfile.gradeEntity',
    'agentProfile.hrFunction',
]);

    $ministries = Ministry::query()
        ->where('active', true)
        ->orderBy('name')
        ->get();

    $services = Service::query()
        ->where('ministry_id', $agent->ministry_id)
        ->where('active', true)
        ->orderBy('name')
        ->get();

    $categories = HrCategory::query()
        ->where('active', true)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

    $grades = HrGrade::query()
        ->where('active', true)
        ->with('category:id,code,name')
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

    $functions = HrFunction::query()
        ->where('active', true)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

    return view(
        'responsable.recruitment.agents.edit',
        compact(
            'agent',
            'ministries',
            'services',
            'categories',
            'grades',
            'functions'
        )
    );
}

   public function update(
    Request $request,
    User $agent
): RedirectResponse {

    abort_unless(
        $agent->role === User::ROLE_AGENT,
        404
    );

    $validated = $request->validate([

        'name' => [
            'required',
            'string',
            'max:255',
        ],

        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('users', 'email')
                ->ignore($agent->id),
        ],
'service_id' => [
    'nullable',
    'integer',
    Rule::exists('services', 'id'),
],

'category_id' => [
    'nullable',
    'integer',
    Rule::exists('hr_categories', 'id'),
],

'grade_id' => [
    'nullable',
    'integer',
    Rule::exists('hr_grades', 'id'),
],

'function_id' => [
    'nullable',
    'integer',
    Rule::exists('hr_functions', 'id'),
],
        'phone' => [
            'nullable',
            'string',
            'max:30',
        ],


        'birth_date' => [
            'nullable',
            'date',
            'before:today',
        ],

        'place_of_birth' => [
            'nullable',
            'string',
            'max:255',
        ],

        'recruitment_date' => [
            'nullable',
            'date',
        ],

        'appointment_date' => [
            'nullable',
            'date',
        ],

        'notes' => [
            'nullable',
            'string',
            'max:3000',
        ],

        'update_reason' => [
            'required',
            'string',
            'max:1000',
        ],

    ]);

    $agent = $this->careerService->updateProfile(
        $agent,
        $validated,
        $request->user(),
        $validated['update_reason']
    );

    return redirect()
        ->route(
            'responsable.agents.show',
            $agent
        )
        ->with(
            'success',
            'Le dossier RH de l’agent a été mis à jour et historisé.'
        );
}

public function history(User $agent): View
{
    abort_unless(
        $agent->role === 'agent',
        404
    );

    $agent->load('ministry');

    $histories = AgentCareerHistory::query()
        ->with([
            'author',
            'fromMinistry',
            'toMinistry',
        ])
        ->where('agent_id', $agent->id)
        ->latest('effective_at')
        ->latest('id')
        ->paginate(20);

    return view(
    'responsable.recruitment.agents.edit',
    compact(
        'agent',
        'ministries',
        'services',
        'grades',
        'categories',
        'jobTitles'
    )
);
}

    public function toggle(
    Request $request,
    User $agent
): RedirectResponse {
    $validated = $request->validate([
        'reason' => [
            'required',
            'string',
            'max:2000',
        ],
        'reference' => [
            'nullable',
            'string',
            'max:100',
        ],
    ]);

    $this->careerService->changeActivation(
        $agent,
        ! $agent->active,
        $request->user(),
        $validated['reason'],
        $validated['reference'] ?? null
    );

    return back()->with(
        'success',
        $agent->active
            ? 'Le compte agent a été désactivé.'
            : 'Le compte agent a été réactivé.'
    );
}
}