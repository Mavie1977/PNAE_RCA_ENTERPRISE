<?php

namespace App\Http\Controllers\Responsable;

use App\Http\Controllers\Controller;
use App\Models\AgentCareerHistory;
use App\Models\AgentDisciplinaryAction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AgentDisciplinaryActionController extends Controller
{
    public function index(User $agent): View
    {
        abort_unless(
            $agent->role === User::ROLE_AGENT,
            404
        );

        $actions = AgentDisciplinaryAction::query()
            ->with([
                'creator',
                'decisionAuthor',
            ])
            ->where('agent_id', $agent->id)
            ->orderByDesc('effective_at')
            ->orderByDesc('id')
            ->get();

        return view(
            'responsable.disciplinary.index',
            compact('agent', 'actions')
        );
    }

    public function create(User $agent): View
    {
        abort_unless(
            $agent->role === User::ROLE_AGENT,
            404
        );

        return view(
            'responsable.disciplinary.create',
            compact('agent')
        );
    }

    public function store(
        Request $request,
        User $agent
    ): RedirectResponse {
        abort_unless(
            $agent->role === User::ROLE_AGENT,
            404
        );

        $validated = $request->validate([
            'sanction_type' => [
                'required',
                Rule::in([
                    AgentDisciplinaryAction::TYPE_WARNING,
                    AgentDisciplinaryAction::TYPE_REPRIMAND,
                    AgentDisciplinaryAction::TYPE_SUSPENSION,
                    AgentDisciplinaryAction::TYPE_TEMPORARY_EXCLUSION,
                    AgentDisciplinaryAction::TYPE_DISMISSAL,
                    AgentDisciplinaryAction::TYPE_OTHER,
                ]),
            ],

            'effective_at' => [
                'required',
                'date',
            ],

            'end_at' => [
                'nullable',
                'date',
                'after_or_equal:effective_at',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:150',
            ],

            'reason' => [
                'required',
                'string',
                'max:3000',
            ],
        ]);

        $action = AgentDisciplinaryAction::create([
            'agent_id' => $agent->id,
            'created_by' => $request->user()->id,

            'sanction_type' =>
                $validated['sanction_type'],

            'status' =>
                AgentDisciplinaryAction::STATUS_PENDING,

            'effective_at' =>
                $validated['effective_at'],

            'end_at' =>
                $validated['end_at'] ?? null,

            'reference' =>
                $validated['reference'] ?? null,

            'reason' =>
                $validated['reason'],
        ]);

        AgentCareerHistory::create([
            'agent_id' => $agent->id,
            'performed_by' => $request->user()->id,

            'from_ministry_id' =>
                $agent->ministry_id,

            'to_ministry_id' =>
                $agent->ministry_id,

            'event_type' =>
                AgentCareerHistory::TYPE_DISCIPLINARY_CREATED,

            'title' =>
                'Procédure disciplinaire enregistrée',

            'reason' =>
                $validated['reason'],

            'reference' =>
                $validated['reference'] ?? null,

            'previous_active' =>
                $agent->active,

            'new_active' =>
                $agent->active,

            'metadata' => [
                'disciplinary_action_id' => $action->id,
                'sanction_type' => $action->sanction_type,
                'status' => $action->status,
            ],

            'effective_at' => now(),
        ]);

        return redirect()
            ->route(
                'responsable.agents.disciplinary.index',
                $agent
            )
            ->with(
                'success',
                'La procédure disciplinaire a été enregistrée.'
            );
    }

    public function approve(
        Request $request,
        User $agent,
        AgentDisciplinaryAction $disciplinary
    ): RedirectResponse {
        abort_unless(
            $disciplinary->agent_id === $agent->id,
            404
        );

        $disciplinary->update([
            'status' =>
                AgentDisciplinaryAction::STATUS_APPROVED,

            'decided_by' =>
                $request->user()->id,

            'decision_reason' =>
                $request->input('decision_reason'),

            'decided_at' =>
                now(),
        ]);

        AgentCareerHistory::create([
            'agent_id' => $agent->id,
            'performed_by' => $request->user()->id,

            'from_ministry_id' =>
                $agent->ministry_id,

            'to_ministry_id' =>
                $agent->ministry_id,

            'event_type' =>
                AgentCareerHistory::TYPE_DISCIPLINARY_APPROVED,

            'title' =>
                'Sanction disciplinaire validée',

            'reason' =>
                $request->input('decision_reason')
                ?: $disciplinary->reason,

            'reference' =>
                $disciplinary->reference,

            'previous_active' =>
                $agent->active,

            'new_active' =>
                $agent->active,

            'metadata' => [
                'disciplinary_action_id' =>
                    $disciplinary->id,

                'sanction_type' =>
                    $disciplinary->sanction_type,

                'status' =>
                    AgentDisciplinaryAction::STATUS_APPROVED,
            ],

            'effective_at' => now(),
        ]);

        return back()->with(
            'success',
            'La sanction disciplinaire a été validée.'
        );
    }

    public function reject(
        Request $request,
        User $agent,
        AgentDisciplinaryAction $disciplinary
    ): RedirectResponse {
        abort_unless(
            $disciplinary->agent_id === $agent->id,
            404
        );

        $validated = $request->validate([
            'decision_reason' => [
                'required',
                'string',
                'max:3000',
            ],
        ]);

        $disciplinary->update([
            'status' =>
                AgentDisciplinaryAction::STATUS_REJECTED,

            'decided_by' =>
                $request->user()->id,

            'decision_reason' =>
                $validated['decision_reason'],

            'decided_at' =>
                now(),
        ]);

        AgentCareerHistory::create([
            'agent_id' => $agent->id,
            'performed_by' => $request->user()->id,

            'from_ministry_id' =>
                $agent->ministry_id,

            'to_ministry_id' =>
                $agent->ministry_id,

            'event_type' =>
                AgentCareerHistory::TYPE_DISCIPLINARY_REJECTED,

            'title' =>
                'Procédure disciplinaire rejetée',

            'reason' =>
                $validated['decision_reason'],

            'reference' =>
                $disciplinary->reference,

            'previous_active' =>
                $agent->active,

            'new_active' =>
                $agent->active,

            'metadata' => [
                'disciplinary_action_id' =>
                    $disciplinary->id,

                'sanction_type' =>
                    $disciplinary->sanction_type,

                'status' =>
                    AgentDisciplinaryAction::STATUS_REJECTED,
            ],

            'effective_at' => now(),
        ]);

        return back()->with(
            'success',
            'La procédure disciplinaire a été rejetée.'
        );
    }
}