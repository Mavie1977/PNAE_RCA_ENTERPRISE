<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMinistryRequest;
use App\Http\Requests\Admin\UpdateMinistryRequest;
use App\Models\Ministry;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class MinistryController extends Controller
{
    public function index(Request $request): View
    {
        $ministries = Ministry::query()
            ->withCount([
                'procedures',
                'users',
            ])
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
                                    'name',
                                    'ilike',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'code',
                                    'ilike',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $request->filled('active'),
                fn ($query) => $query->where(
                    'active',
                    $request->boolean('active')
                )
            )
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'admin.ministries.index',
            compact('ministries')
        );
    }

    public function create(): View
    {
        return view('admin.ministries.create');
    }

    public function store(
    StoreMinistryRequest $request
): RedirectResponse {
    $data = $request->validated();

    $data['name'] = trim($data['name']);

    $baseSlug = Str::slug($data['name']);
    $slug = $baseSlug;
    $counter = 2;

    while (Ministry::where('slug', $slug)->exists()) {
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }

    $data['slug'] = $slug;

    $data['active'] = $request->boolean('active', true);

    $ministry = Ministry::create($data);

    AuditService::log(
        'Création ministère',
        'Ministère',
        $ministry->id,
        [
            'name' => $ministry->name,
            'slug' => $ministry->slug,
        ]
    );

    return redirect()
        ->route('admin.ministries.show', $ministry)
        ->with(
            'success',
            'Le ministère a été créé avec succès.'
        );
}

public function show(Ministry $ministry): View
{
    $ministry->load([
        'procedures' => function ($query) {
            $query->orderBy('title');
        },

        'users' => function ($query) {
            $query->orderBy('name');
        },
    ]);

    return view(
        'admin.ministries.show',
        compact('ministry')
    );
}

    public function edit(Ministry $ministry): View
    {
        return view(
            'admin.ministries.edit',
            compact('ministry')
        );
    }

    public function update(
        UpdateMinistryRequest $request,
        Ministry $ministry
    ): RedirectResponse {
        $before = $ministry->toArray();

        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        $ministry->update($data);

        AuditService::log(
            'Modification ministère',
            'Ministère',
            $ministry->id,
            [
                'before' => $before,
                'after' => $ministry->fresh()->toArray(),
            ]
        );

        return redirect()
            ->route('admin.ministries.index')
            ->with(
                'success',
                'Le ministère a été mis à jour.'
            );
    }

    public function toggle(
        Ministry $ministry
    ): RedirectResponse {
        $ministry->update([
            'active' => ! $ministry->active,
        ]);

        /*
         * Si le ministère est désactivé,
         * ses démarches sont aussi désactivées.
         */
        if (! $ministry->active) {
            $ministry->procedures()->update([
                'active' => false,
            ]);
        }

        AuditService::log(
            $ministry->active
                ? 'Activation ministère'
                : 'Désactivation ministère',
            'Ministère',
            $ministry->id,
            [
                'active' => $ministry->active,
            ]
        );

        return back()->with(
            'success',
            $ministry->active
                ? 'Le ministère a été activé.'
                : 'Le ministère et ses démarches ont été désactivés.'
        );
    }

    public function destroy(
        Ministry $ministry
    ): RedirectResponse {
        if (
            $ministry->procedures()->exists()
            || $ministry->users()->exists()
        ) {
            return back()->withErrors([
                'ministry' =>
                    'Ce ministère possède des démarches ou des utilisateurs. Désactivez-le au lieu de le supprimer.',
            ]);
        }

        $data = $ministry->toArray();
        $ministry->delete();

        AuditService::log(
            'Suppression ministère',
            'Ministère',
            $data['id'],
            $data
        );

        return redirect()
            ->route('admin.ministries.index')
            ->with(
                'success',
                'Le ministère a été supprimé.'
            );
    }
}