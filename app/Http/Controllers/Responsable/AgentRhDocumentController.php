<?php

namespace App\Http\Controllers\Responsable;

use App\Http\Controllers\Controller;
use App\Models\AgentRhDocument;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\AgentCareerHistory;

class AgentRhDocumentController extends Controller
{
    public function index(User $agent): View
    {
        abort_unless(
            $agent->role === User::ROLE_AGENT,
            404
        );

        $documents = AgentRhDocument::query()
            ->with('uploader')
            ->where('agent_id', $agent->id)
            ->orderByDesc('document_date')
            ->orderByDesc('id')
            ->get();

        return view(
            'responsable.documents.index',
            compact('agent', 'documents')
        );
    }

    public function create(User $agent): View
    {
        abort_unless(
            $agent->role === User::ROLE_AGENT,
            404
        );

        return view(
            'responsable.documents.create',
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
            'document_type' => [
                'required',
                'string',
                'max:60',
            ],
            'title' => [
                'required',
                'string',
                'max:180',
            ],
            'reference' => [
                'nullable',
                'string',
                'max:150',
            ],
            'document_date' => [
                'nullable',
                'date',
            ],
            'file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:3000',
            ],
        ]);

        $file = $request->file('file');

        $path = $file->store(
            'rh-documents/' . $agent->id,
            'local'
        );

        $document = AgentRhDocument::create([
            'agent_id' => $agent->id,
            'uploaded_by' => $request->user()->id,
            'document_type' => $validated['document_type'],
            'title' => $validated['title'],
            'reference' => $validated['reference'] ?? null,
            'document_date' => $validated['document_date'] ?? null,
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'notes' => $validated['notes'] ?? null,
            'active' => true,
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
        AgentCareerHistory::TYPE_RH_DOCUMENT_ADDED,

    'title' =>
        'Document RH ajouté',

    'reason' =>
        $document->title,

    'reference' =>
        $document->reference,

    'previous_active' =>
        $agent->active,

    'new_active' =>
        $agent->active,

    'metadata' => [
        'rh_document_id' =>
            $document->id,

        'document_type' =>
            $document->document_type,

        'document_title' =>
            $document->title,

        'original_name' =>
            $document->original_name,

        'mime_type' =>
            $document->mime_type,

        'file_size' =>
            $document->file_size,

        'document_date' =>
            $document->document_date?->format('Y-m-d'),

        'active' => true,
    ],

    'effective_at' => now(),
]);

        return redirect()
            ->route(
                'responsable.agents.documents.index',
                $agent
            )
            ->with(
                'success',
                'Le document RH a été ajouté au dossier de l’agent.'
            );
    }

    public function download(
        User $agent,
        AgentRhDocument $document
    ): StreamedResponse {
        abort_unless(
            $document->agent_id === $agent->id,
            404
        );

        abort_unless(
            Storage::disk('local')->exists($document->file_path),
            404
        );

        return Storage::disk('local')->download(
            $document->file_path,
            $document->original_name
        );
    }

    public function archive(
    Request $request,
    User $agent,
    AgentRhDocument $document
): RedirectResponse {

    abort_unless(
        $document->agent_id === $agent->id,
        404
    );

    if (!$document->active) {
        return back()->with(
            'success',
            'Ce document RH est déjà archivé.'
        );
    }

    $document->update([
        'active' => false,
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
            AgentCareerHistory::TYPE_RH_DOCUMENT_ARCHIVED,

        'title' =>
            'Document RH archivé',

        'reason' =>
            $document->title,

        'reference' =>
            $document->reference,

        'previous_active' =>
            $agent->active,

        'new_active' =>
            $agent->active,

        'metadata' => [
            'rh_document_id' =>
                $document->id,

            'document_type' =>
                $document->document_type,

            'document_title' =>
                $document->title,

            'original_name' =>
                $document->original_name,

            'active' => false,
        ],

        'effective_at' =>
            now(),
    ]);

    return back()->with(
        'success',
        'Le document RH a été archivé et l’opération historisée.'
    );
}
}