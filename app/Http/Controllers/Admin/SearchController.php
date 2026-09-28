<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Ministry;
use App\Models\Procedure;
use App\Models\User;
use Illuminate\Http\Request;


class SearchController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q'));

        $users = collect();
        $applications = collect();
        $ministries = collect();
        $procedures = collect();

        if ($search !== '') {
            $users = User::query()
                ->where(function ($query) use ($search) {
                    $query
                        ->where('name', 'ilike', '%' . $search . '%')
                        ->orWhere('email', 'ilike', '%' . $search . '%')
                        ->orWhere('phone', 'ilike', '%' . $search . '%');
                })
                ->latest()
                ->take(15)
                ->get();

            $applications = Application::with([
                'user',
                'procedure.ministry',
            ])
                ->where(function ($query) use ($search) {
                    $query
                        ->where('reference', 'ilike', '%' . $search . '%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery
                                ->where('name', 'ilike', '%' . $search . '%')
                                ->orWhere('email', 'ilike', '%' . $search . '%');
                        })
                        ->orWhereHas('procedure', function ($procedureQuery) use ($search) {
                            $procedureQuery->where(
                                'title',
                                'ilike',
                                '%' . $search . '%'
                            );
                        });
                })
                ->latest()
                ->take(20)
                ->get();

            $ministries = Ministry::query()
                ->where('name', 'ilike', '%' . $search . '%')
                ->orderBy('name')
                ->take(10)
                ->get();

            $procedures = Procedure::with('ministry')
                ->where('title', 'ilike', '%' . $search . '%')
                ->orderBy('title')
                ->take(15)
                ->get();
        }

        return view('admin.search.index', compact(
            'search',
            'users',
            'applications',
            'ministries',
            'procedures'
        ));
    }
	public function showApplication(Application $application)
{
    $application->load([
        'user',
        'procedure.ministry',
        'documents',
        'workflowLogs.user',
        'assignedAgent',
    ]);

    return view(
        'admin.applications.show',
        compact('application')
    );
}
}