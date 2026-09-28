<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function showForm(): View
    {
        return view('public.tracking.form');
    }

    public function search(Request $request): View
    {
        $validated = $request->validate([
            'reference' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:190',
            ],
        ], [
            'reference.required' =>
                'Veuillez saisir la référence de la demande.',

            'email.required' =>
                'Veuillez saisir l’adresse électronique utilisée lors du dépôt.',

            'email.email' =>
                'L’adresse électronique saisie est invalide.',
        ]);

        $application = Application::query()
            ->with([
                'procedure.ministry',
                'documents',
            ])
            ->where('reference', trim($validated['reference']))
            ->whereHas(
                'user',
                fn ($query) => $query->where(
                    'email',
                    trim($validated['email'])
                )
            )
            ->first();

        return view('public.tracking.form', [
            'application' => $application,
            'searched' => true,
        ]);
    }
}