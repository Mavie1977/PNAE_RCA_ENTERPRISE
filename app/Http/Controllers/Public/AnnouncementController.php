<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::query()
            ->where('active', true)
            ->latest()
            ->paginate(12);

        return view(
            'public.announcements.index',
            compact('announcements')
        );
    }
}