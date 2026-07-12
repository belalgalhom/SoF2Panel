<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $externalAuth = [
            'enabled' => Setting::get('external_auth_enabled', false),
            'type' => Setting::get('external_auth_type', 'XenForo'),
            'host' => Setting::get('external_auth_host', '127.0.0.1'),
            'port' => Setting::get('external_auth_port', '3306'),
            'database' => Setting::get('external_auth_database', 'xenforo'),
            'username' => Setting::get('external_auth_username', 'root'),
            'password' => Setting::get('external_auth_password', ''),
            'table' => Setting::get('external_auth_table', 'xf_user'),
            'col_id' => Setting::get('external_auth_col_id', 'user_id'),
            'col_username' => Setting::get('external_auth_col_username', 'username'),
            'col_email' => Setting::get('external_auth_col_email', 'email'),
            'col_password' => Setting::get('external_auth_col_password', 'secret_key'),
        ];

        return view('admin.settings.index', compact('externalAuth'));
    }

    public function updateExternalAuth(Request $request)
    {
        $request->validate([
            'external_auth_enabled' => 'boolean',
            'external_auth_type' => 'required|in:generic,XenForo',
            'external_auth_host' => 'required|string',
            'external_auth_port' => 'required|numeric',
            'external_auth_database' => 'required|string',
            'external_auth_username' => 'required|string',
            'external_auth_password' => 'nullable|string',
            'external_auth_table' => 'required|string',
            'external_auth_col_id' => 'required|string',
            'external_auth_col_username' => 'required|string',
            'external_auth_col_email' => 'required|string',
            'external_auth_col_password' => 'required|string',
        ]);

        Setting::set('external_auth_enabled', $request->boolean('external_auth_enabled'));
        Setting::set('external_auth_type', $request->external_auth_type);
        Setting::set('external_auth_host', $request->external_auth_host);
        Setting::set('external_auth_port', $request->external_auth_port);
        Setting::set('external_auth_database', $request->external_auth_database);
        Setting::set('external_auth_username', $request->external_auth_username);
        
        if ($request->has('external_auth_password')) {
            Setting::set('external_auth_password', $request->external_auth_password);
        }

        Setting::set('external_auth_table', $request->external_auth_table);
        Setting::set('external_auth_col_id', $request->external_auth_col_id);
        Setting::set('external_auth_col_username', $request->external_auth_col_username);
        Setting::set('external_auth_col_email', $request->external_auth_col_email);
        Setting::set('external_auth_col_password', $request->external_auth_col_password);

        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'action' => 'Updated Settings',
            'target' => 'External Authentication',
            'ip' => request()->ip()
        ]);

        return back()->with('success', 'External Authentication settings updated successfully.');
    }
}
