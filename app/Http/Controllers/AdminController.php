<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Ministry;
use App\Models\Procedure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Announcement;
use App\Models\Setting;
use App\Services\AuditService;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'citizens' => User::where('role', 'citoyen')->count(),
            'agents' => User::where('role', 'agent')->count(),
            'ministries' => Ministry::count(),
            'procedures' => Procedure::count(),
            'applications' => Application::count(),
            'submitted' => Application::where('status', 'soumise')->count(),
            'processing' => Application::where('status', 'en_traitement')->count(),
            'validated' => Application::where('status', 'validee')->count(),
        ];

        $latestApplications = Application::with(['user', 'procedure.ministry'])->latest()->take(10)->get();
        $latestUsers = User::latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'latestApplications', 'latestUsers'));
    }


public function announcements()
{
    $announcements = Announcement::latest()->get();

    return view('admin.announcements.index', compact('announcements'));
}

public function createAnnouncement()
{
    return view('admin.announcements.create');
}

public function storeAnnouncement(Request $request)
{
    $validated = $request->validate([
        'title' => ['required', 'string', 'max:150'],
        'content' => ['nullable', 'string'],
        'type' => ['required', 'string', 'max:50'],
        'start_date' => ['nullable', 'date'],
        'end_date' => ['nullable', 'date'],
    ]);

    Announcement::create([
        'title' => $validated['title'],
        'content' => $validated['content'] ?? null,
        'type' => $validated['type'],
        'start_date' => $validated['start_date'] ?? null,
        'end_date' => $validated['end_date'] ?? null,
        'active' => true,
    ]);

    return redirect()
        ->route('admin.announcements.index')
        ->with('success', 'Annonce créée avec succès.');
}

public function showAnnouncement(Announcement $announcement)
{
    return view('admin.announcements.show', compact('announcement'));
}

public function toggleAnnouncement(Announcement $announcement)
{
    $announcement->update([
        'active' => ! $announcement->active,
    ]);

    return back()->with('success', 'Statut de l’annonce modifié avec succès.');
}


    public function citizens()
    {
        $citizens = User::where('role', 'citoyen')->withCount('applications')->latest()->get();
        return view('admin.citizens.index', compact('citizens'));
    }

    public function showCitizen(User $user)
    {
        abort_if($user->role !== 'citoyen', 404);
        $user->load(['applications.procedure', 'applications.documents']);
        return view('admin.citizens.show', compact('user'));
    }

    public function agents()
    {
        $agents = User::where('role', 'agent')->latest()->get();
        return view('admin.agents.index', compact('agents'));
    }

    public function createAgent()
    {
        return view('admin.agents.create');
    }

    public function storeAgent(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'agent',
            'active' => true,
        ]);

        return redirect()->route('admin.agents.index')->with('success', 'Agent public créé avec succès.');
    }

    public function showAgent(User $user)
    {
        abort_if($user->role !== 'agent', 404);
        $applications = Application::with(['user', 'procedure'])->latest()->take(20)->get();
        return view('admin.agents.show', compact('user', 'applications'));
    }

    public function toggleUser(User $user)
    {
        $user->update(['active' => ! $user->active]);
        return back()->with('success', 'Statut du compte modifié avec succès.');
    }

    public function ministries()
    {
        $ministries = Ministry::withCount('procedures')->orderBy('name')->get();
        return view('admin.ministries.index', compact('ministries'));
    }

    public function createMinistry()
    {
        return view('admin.ministries.create');
    }

    public function storeMinistry(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:ministries,name'],
            'description' => ['nullable', 'string'],
        ]);

        Ministry::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'active' => true,
        ]);

        return redirect()->route('admin.ministries.index')->with('success', 'Ministère créé avec succès.');
    }

    public function showMinistry(Ministry $ministry)
    {
        $ministry->load('procedures');
        return view('admin.ministries.show', compact('ministry'));
    }

    public function toggleMinistry(Ministry $ministry)
    {
        $ministry->update(['active' => ! $ministry->active]);
        return back()->with('success', 'Statut du ministère modifié avec succès.');
    }

    public function procedures()
    {
        $procedures = Procedure::with('ministry')->orderBy('title')->get();
        return view('admin.procedures.index', compact('procedures'));
    }

    public function createProcedure()
    {
        $ministries = Ministry::where('active', true)->orderBy('name')->get();
        return view('admin.procedures.create', compact('ministries'));
    }

    public function storeProcedure(Request $request)
    {
        $validated = $request->validate([
            'ministry_id' => ['required', 'exists:ministries,id'],
            'title' => ['required', 'string', 'max:150', 'unique:procedures,title'],
            'description' => ['nullable', 'string'],
            'required_documents' => ['nullable', 'string'],
            'fee' => ['nullable', 'numeric', 'min:0'],
            'processing_days' => ['nullable', 'integer', 'min:1'],
        ]);

        Procedure::create([
            'ministry_id' => $validated['ministry_id'],
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'] ?? null,
            'required_documents' => $validated['required_documents'] ?? null,
            'fee' => $validated['fee'] ?? 0,
            'processing_days' => $validated['processing_days'] ?? 7,
            'active' => true,
        ]);

        return redirect()->route('admin.procedures.index')->with('success', 'Démarche créée avec succès.');
    }

    public function showProcedure(Procedure $procedure)
    {
        $procedure->load('ministry');
        return view('admin.procedures.show', compact('procedure'));
    }

    public function toggleProcedure(Procedure $procedure)
    {
        $procedure->update(['active' => ! $procedure->active]);
        return back()->with('success', 'Statut de la démarche modifié avec succès.');
    }
	public function settings()
{
    $settings = Setting::all()->pluck('value', 'key');

    return view('admin.settings.index', compact('settings'));
}

public function updateSettings(Request $request)
{
    $fields = [
        'portal_name',
        'portal_short_name',
        'country',
        'contact_email',
        'contact_phone',
        'address',
        'footer_text',
        'facebook',
        'twitter',
        'linkedin',
    ];

    foreach ($fields as $field) {

        Setting::setValue(
            $field,
            $request->$field
        );

    }

    Setting::setValue(
        'maintenance_mode',
        $request->has('maintenance_mode') ? 1 : 0,
        'boolean'
    );

    return redirect()
        ->route('admin.settings.index')
        ->with('success', 'Paramètres enregistrés avec succès.');
}
}