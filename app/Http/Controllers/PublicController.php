<?php

namespace App\Http\Controllers;

use App\Models\Ministry;
use App\Models\Procedure;
use App\Services\AuditService;

class PublicController extends Controller
{
    public function home()
    {
        $ministries = Ministry::withCount([
            'procedures' => fn ($query) => $query->where('active', true)
        ])
        ->where('active', true)
        ->orderBy('name')
        ->take(5)
        ->get();

        return view('public.home', compact('ministries'));
    }

    public function services()
    {
        $procedures = Procedure::with('ministry')
            ->where('active', true)
            ->orderBy('title')
            ->get();

        return view('public.services', compact('procedures'));
    }

    public function servicesByMinistry(Ministry $ministry)
    {
        $procedures = Procedure::with('ministry')
            ->where('ministry_id', $ministry->id)
            ->where('active', true)
            ->orderBy('title')
            ->get();

        return view('public.services', compact('procedures', 'ministry'));
    }

    public function contact()
    {
        return view('public.contact');
    }
}