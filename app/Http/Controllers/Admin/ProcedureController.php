<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProcedureRequest;
use App\Http\Requests\Admin\UpdateProcedureRequest;
use App\Models\Ministry;
use App\Models\Procedure;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProcedureController extends Controller
{
    public function index(Request $request): View
    {
        $procedures = Procedure::query()
            ->with('ministry')
            ->withCount('applications')
            ->when(
                $request->filled('q'),
                function ($query) use ($request): void {
                    $search = trim(
                        (string) $request->query('q')
                    );

                    $query->where(
                        function ($subQuery) use ($search): void {
                            $subQuery
                                ->where(
                                    'title',
                                    'ilike',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'description',
                                    'ilike',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $request->filled('ministry_id'),
                fn ($query) => $query->where(
                    'ministry_id',
                    $request->integer('ministry_id')
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

        $ministries = Ministry::query()
            ->orderBy('name')
            ->get();

        return view(
            'admin.procedures.index',
            compact('procedures', 'ministries')
        );
    }

    public function create(): View
    {
        $ministries = Ministry::query()
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return view(
            'admin.procedures.create',
            compact('ministries')
        );
    }

    public function store(
        StoreProcedureRequest $request
    ): RedirectResponse {
        $data = $this->prepareData($request);

        $procedure = Procedure::create($data);

        AuditService::log(
            'Création démarche',
            'Démarche',
            $procedure->id,
            [
                'title' => $procedure->title,
                'ministry_id' => $procedure->ministry_id,
                'fee' => $procedure->fee,
            ]
        );

        return redirect()
            ->route('admin.procedures.index')
            ->with(
                'success',
                'La démarche a été créée avec succès.'
            );
    }

    public function show(Procedure $procedure): View
{
    $procedure->load('ministry');
    $procedure->loadCount('applications');

    return view(
        'admin.procedures.show',
        compact('procedure')
    );
}
    public function edit(Procedure $procedure): View
{
    $ministries = Ministry::query()
        ->orderBy('name')
        ->get();

    return view(
        'admin.procedures.edit',
        compact('procedure', 'ministries')
    );
}

    public function update(
    UpdateProcedureRequest $request,
    Procedure $procedure
): RedirectResponse {
    $before = $procedure->toArray();

    $procedure->update(
        $this->prepareData($request, $procedure)
    );

    AuditService::log(
        'Modification démarche',
        'Démarche',
        $procedure->id,
        [
            'before' => $before,
            'after' => $procedure->fresh()->toArray(),
        ]
    );

    return redirect()
        ->route('admin.procedures.show', [
            'procedure' => $procedure->id,
        ])
        ->with(
            'success',
            'La démarche a été mise à jour avec succès.'
        );
}

    public function toggle(
    Procedure $procedure
): RedirectResponse {
    if (
        ! $procedure->active
        && ! $procedure->ministry?->active
    ) {
        return back()->withErrors([
            'procedure' =>
                'Impossible d’activer une démarche appartenant à un ministère désactivé.',
        ]);
    }

    $procedure->update([
        'active' => ! $procedure->active,
    ]);

    AuditService::log(
        $procedure->active
            ? 'Activation démarche'
            : 'Désactivation démarche',
        'Démarche',
        $procedure->id,
        [
            'active' => $procedure->active,
        ]
    );

    return back()->with(
        'success',
        $procedure->active
            ? 'La démarche a été activée.'
            : 'La démarche a été désactivée.'
    );
}
    public function destroy(
        Procedure $procedure
    ): RedirectResponse {
        if ($procedure->applications()->exists()) {
            return back()->withErrors([
                'procedure' =>
                    'Cette démarche possède des dossiers. Désactivez-la au lieu de la supprimer.',
            ]);
        }

        $data = $procedure->toArray();
        $procedure->delete();

        AuditService::log(
            'Suppression démarche',
            'Démarche',
            $data['id'],
            $data
        );

        return redirect()
            ->route('admin.procedures.index')
            ->with(
                'success',
                'La démarche a été supprimée.'
            );
    }


    private function prepareData(
        Request $request,
        ?Procedure $procedure = null
    ): array {
        $data = $request->validated();

        $baseSlug = Str::slug($data['title']);
        $slug = $baseSlug;
        $counter = 2;

        while (
            Procedure::where('slug', $slug)
                ->when(
                    $procedure,
                    fn ($query) => $query->whereKeyNot(
                        $procedure->id
                    )
                )
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $data['slug'] = $slug;
        $data['payment_required'] =
            $request->boolean('payment_required');

        $data['official_document_required'] =
            $request->boolean(
                'official_document_required',
                true
            );

        $data['active'] =
            $request->boolean('active', true);

        if (! $data['payment_required']) {
            $data['fee'] = 0;
        }

        return $data;
    }
}