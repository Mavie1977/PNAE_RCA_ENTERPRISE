<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Services\AuditService;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->get();

        return view(
            'admin.announcements.index',
            compact('announcements')
        );
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'content' => ['nullable', 'string'],
            'type' => ['required', 'string', 'max:50'],
            'start_date' => ['nullable', 'date'],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
        ]);

        $announcement = Announcement::create([
            ...$validated,
            'active' => true,
        ]);

        AuditService::log(
            'Création',
            'Annonce',
            $announcement->id,
            ['title' => $announcement->title]
        );

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Annonce créée avec succès.');
    }

    public function show(Announcement $announcement)
    {
        return view(
            'admin.announcements.show',
            compact('announcement')
        );
    }

    public function toggle(Announcement $announcement)
    {
        $announcement->update([
            'active' => ! $announcement->active,
        ]);

        AuditService::log(
            'Changement de statut',
            'Annonce',
            $announcement->id,
            [
                'title' => $announcement->title,
                'active' => $announcement->active,
            ]
        );

        return back()->with(
            'success',
            'Statut de l’annonce modifié avec succès.'
        );
    }
}