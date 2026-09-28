<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\UpdateDocumentStatusRequest;
use App\Models\ApplicationDocument;
use App\Services\DocumentService;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function show(
        ApplicationDocument $document
    ): StreamedResponse {
        abort_unless(
            Storage::disk('public')->exists($document->file_path),
            404,
            'Le document demandé est introuvable.'
        );

        return Storage::disk('public')->response(
            $document->file_path,
            $document->display_name,
            [
                'Content-Type' => $document->mime_type
                    ?? 'application/octet-stream',
                'Content-Disposition' => 'inline',
            ]
        );
    }

    public function download(
        ApplicationDocument $document
    ): StreamedResponse {
        abort_unless(
            Storage::disk('public')->exists($document->file_path),
            404,
            'Le document demandé est introuvable.'
        );

        return Storage::disk('public')->download(
            $document->file_path,
            $document->display_name
        );
    }

    public function updateStatus(
        UpdateDocumentStatusRequest $request,
        ApplicationDocument $document,
        DocumentService $documentService
    ) {
        $validated = $request->validated();

        $documentService->updateStatus(
            $document,
            $validated['status'],
            $validated['note'] ?? null
        );

        return redirect()
            ->route(
                'agent.applications.show',
                $document->application_id
            )
            ->with(
                'success',
                'Le contrôle du document a été enregistré.'
            );
    }
}