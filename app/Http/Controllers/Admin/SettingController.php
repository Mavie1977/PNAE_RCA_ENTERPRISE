<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\AuditService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');

        return view(
            'admin.settings.index',
            compact('settings')
        );
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'portal_name' => ['nullable', 'string', 'max:255'],
            'portal_short_name' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'footer_text' => ['nullable', 'string'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'twitter' => ['nullable', 'string', 'max:255'],
            'linkedin' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::setValue($key, $value);
        }

        Setting::setValue(
            'maintenance_mode',
            $request->boolean('maintenance_mode') ? '1' : '0',
            'boolean'
        );

        AuditService::log(
            'Modification',
            'Paramètres',
            null,
            ['fields' => array_keys($validated)]
        );

        return back()->with(
            'success',
            'Paramètres enregistrés avec succès.'
        );
    }
}