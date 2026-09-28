<?php

namespace App\Http\Controllers\Responsable;

use App\Http\Controllers\Controller;
use App\Models\AgentCareerHistory;
use App\Models\AgentLeave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AgentLeaveController extends Controller
{
    public function index(User $agent): View
    {
        abort_unless(
            $agent->role === User::ROLE_AGENT,
            404
        );

        $leaves = AgentLeave::query()
            ->with([
                'creator',
                'decisionAuthor',
            ])
            ->where('agent_id', $agent->id)
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->get();

        return view(
            'responsable.leaves.index',
            compact(
                'agent',
                'leaves'
            )
        );
    }

    public function create(User $agent): View
    {
        abort_unless(
            $agent->role === User::ROLE_AGENT,
            404
        );

        return view(
            'responsable.leaves.create',
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
            'leave_type' => [
                'required',
                Rule::in([
                    AgentLeave::TYPE_ANNUAL,
                    AgentLeave::TYPE_SICK,
                    AgentLeave::TYPE_MATERNITY,
                    AgentLeave::TYPE_PATERNITY,
                    AgentLeave::TYPE_ADMINISTRATIVE,
                    AgentLeave::TYPE_TRAINING,
                    AgentLeave::TYPE_AUTHORIZED_ABSENCE,
                    AgentLeave::TYPE_UNJUSTIFIED_ABSENCE,
                    AgentLeave::TYPE_OTHER,
                ]),
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:150',
            ],

            'reason' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $start = Carbon::parse(
            $validated['start_date']
        );

        $end = Carbon::parse(
            $validated['end_date']
        );

        $daysCount =
            $start->diffInDays($end) + 1;

        $leave = AgentLeave::create([
            'agent_id' => $agent->id,
            'created_by' => $request->user()->id,

            'leave_type' =>
                $validated['leave_type'],

            'start_date' =>
                $validated['start_date'],

            'end_date' =>
                $validated['end_date'],

            'days_count' =>
                $daysCount,

            'status' =>
                AgentLeave::STATUS_PENDING,

            'reference' =>
                $validated['reference'] ?? null,

            'reason' =>
                $validated['reason'],
        ]);

        AgentCareerHistory::create([
            'agent_id' =>
                $agent->id,

            'performed_by' =>
                $request->user()->id,

            'from_ministry_id' =>
                $agent->ministry_id,

            'to_ministry_id' =>
                $agent->ministry_id,

            'event_type' =>
                AgentCareerHistory::TYPE_LEAVE_CREATED,

            'title' =>
                'Congé / absence enregistré',

            'reason' =>
                $validated['reason'],

            'reference' =>
                $validated['reference'] ?? null,

            'previous_active' =>
                $agent->active,

            'new_active' =>
                $agent->active,

            'metadata' => [
                'leave_id' =>
                    $leave->id,

                'leave_type' =>
                    $leave->leave_type,

                'start_date' =>
                    $leave->start_date->format('Y-m-d'),

                'end_date' =>
                    $leave->end_date->format('Y-m-d'),

                'days_count' =>
                    $leave->days_count,

                'status' =>
                    $leave->status,
            ],

            'effective_at' =>
                now(),
        ]);

        return redirect()
            ->route(
                'responsable.agents.leaves.index',
                $agent
            )
            ->with(
                'success',
                'Le congé ou l’absence a été enregistré.'
            );
    }

    public function approve(
        Request $request,
        User $agent,
        AgentLeave $leave
    ): RedirectResponse {

        abort_unless(
            $leave->agent_id === $agent->id,
            404
        );

        $leave->update([
            'status' =>
                AgentLeave::STATUS_APPROVED,

            'decided_by' =>
                $request->user()->id,

            'decision_reason' =>
                $request->input('decision_reason'),

            'decided_at' =>
                now(),
        ]);

        AgentCareerHistory::create([
            'agent_id' =>
                $agent->id,

            'performed_by' =>
                $request->user()->id,

            'from_ministry_id' =>
                $agent->ministry_id,

            'to_ministry_id' =>
                $agent->ministry_id,

            'event_type' =>
                AgentCareerHistory::TYPE_LEAVE_APPROVED,

            'title' =>
                'Congé / absence validé',

            'reason' =>
                $request->input(
                    'decision_reason'
                ),

            'reference' =>
                $leave->reference,

            'previous_active' =>
                $agent->active,

            'new_active' =>
                $agent->active,

            'metadata' => [
                'leave_id' =>
                    $leave->id,

                'status' =>
                    AgentLeave::STATUS_APPROVED,
            ],

            'effective_at' =>
                now(),
        ]);

        return back()->with(
            'success',
            'Le congé ou l’absence a été validé.'
        );
    }

    public function reject(
        Request $request,
        User $agent,
        AgentLeave $leave
    ): RedirectResponse {

        abort_unless(
            $leave->agent_id === $agent->id,
            404
        );

        $request->validate([
            'decision_reason' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $leave->update([
            'status' =>
                AgentLeave::STATUS_REJECTED,

            'decided_by' =>
                $request->user()->id,

            'decision_reason' =>
                $request->input(
                    'decision_reason'
                ),

            'decided_at' =>
                now(),
        ]);

        AgentCareerHistory::create([
            'agent_id' =>
                $agent->id,

            'performed_by' =>
                $request->user()->id,

            'from_ministry_id' =>
                $agent->ministry_id,

            'to_ministry_id' =>
                $agent->ministry_id,

            'event_type' =>
                AgentCareerHistory::TYPE_LEAVE_REJECTED,

            'title' =>
                'Congé / absence refusé',

            'reason' =>
                $request->input(
                    'decision_reason'
                ),

            'reference' =>
                $leave->reference,

            'previous_active' =>
                $agent->active,

            'new_active' =>
                $agent->active,

            'metadata' => [
                'leave_id' =>
                    $leave->id,

                'status' =>
                    AgentLeave::STATUS_REJECTED,
            ],

            'effective_at' =>
                now(),
        ]);

        return back()->with(
            'success',
            'Le congé ou l’absence a été refusé.'
        );
    }
}