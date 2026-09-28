<?php

namespace App\Http\Controllers\Responsable;

use App\Http\Controllers\Controller;
use App\Models\AgentCareerHistory;
use App\Models\AgentTraining;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AgentTrainingController extends Controller
{
    public function index(User $agent): View
    {
        abort_unless(
            $agent->role === User::ROLE_AGENT,
            404
        );

        $trainings = AgentTraining::query()
            ->with([
                'creator',
                'skills',
            ])
            ->where('agent_id', $agent->id)
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->get();

        return view(
            'responsable.trainings.index',
            compact('agent', 'trainings')
        );
    }

    public function create(User $agent): View
    {
        abort_unless(
            $agent->role === User::ROLE_AGENT,
            404
        );

        return view(
            'responsable.trainings.create',
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
            'title' => [
                'required',
                'string',
                'max:180',
            ],

            'organization' => [
                'nullable',
                'string',
                'max:180',
            ],

            'training_type' => [
                'required',
                Rule::in([
                    AgentTraining::TYPE_TRAINING,
                    AgentTraining::TYPE_SEMINAR,
                    AgentTraining::TYPE_WORKSHOP,
                    AgentTraining::TYPE_CERTIFICATION,
                    AgentTraining::TYPE_CONTINUING_EDUCATION,
                    AgentTraining::TYPE_OTHER,
                ]),
            ],

            'start_date' => [
                'nullable',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'status' => [
                'required',
                Rule::in([
                    AgentTraining::STATUS_PLANNED,
                    AgentTraining::STATUS_IN_PROGRESS,
                    AgentTraining::STATUS_COMPLETED,
                    AgentTraining::STATUS_CANCELLED,
                ]),
            ],

            'certificate_reference' => [
                'nullable',
                'string',
                'max:150',
            ],

            'certificate' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240',
            ],

            'description' => [
                'nullable',
                'string',
                'max:3000',
            ],

            'result' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $path = null;
        $originalName = null;
        $mimeType = null;
        $fileSize = null;

        if ($request->hasFile('certificate')) {
            $file = $request->file('certificate');

            $path = $file->store(
                'rh-trainings/' . $agent->id,
                'local'
            );

            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();
        }

        $training = AgentTraining::create([
            'agent_id' => $agent->id,
            'created_by' => $request->user()->id,

            'title' => $validated['title'],
            'organization' => $validated['organization'] ?? null,
            'training_type' => $validated['training_type'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'status' => $validated['status'],

            'certificate_reference' =>
                $validated['certificate_reference'] ?? null,

            'certificate_file_path' => $path,
            'certificate_original_name' => $originalName,
            'certificate_mime_type' => $mimeType,
            'certificate_file_size' => $fileSize,

            'description' => $validated['description'] ?? null,
            'result' => $validated['result'] ?? null,
        ]);

        AgentCareerHistory::create([
            'agent_id' => $agent->id,
            'performed_by' => $request->user()->id,
            'from_ministry_id' => $agent->ministry_id,
            'to_ministry_id' => $agent->ministry_id,

            'event_type' =>
                AgentCareerHistory::TYPE_TRAINING_ADDED,

            'title' =>
                'Formation enregistrée',

            'reason' =>
                $training->title,

            'reference' =>
                $training->certificate_reference,

            'previous_active' =>
                $agent->active,

            'new_active' =>
                $agent->active,

            'metadata' => [
                'training_id' => $training->id,
                'training_type' => $training->training_type,
                'organization' => $training->organization,
                'status' => $training->status,
                'start_date' => $training->start_date?->format('Y-m-d'),
                'end_date' => $training->end_date?->format('Y-m-d'),
            ],

            'effective_at' => now(),
        ]);

        return redirect()
            ->route(
                'responsable.agents.trainings.index',
                $agent
            )
            ->with(
                'success',
                'La formation a été ajoutée au dossier RH.'
            );
    }

    public function downloadCertificate(
        User $agent,
        AgentTraining $training
    ): StreamedResponse {
        abort_unless(
            $training->agent_id === $agent->id,
            404
        );

        abort_unless(
            $training->certificate_file_path
            && Storage::disk('local')
                ->exists($training->certificate_file_path),
            404
        );

        return Storage::disk('local')->download(
            $training->certificate_file_path,
            $training->certificate_original_name
                ?: 'certificat'
        );
    }
}