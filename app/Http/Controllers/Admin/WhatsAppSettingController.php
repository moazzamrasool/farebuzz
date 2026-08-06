<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Company-owner-only — mirrors AiPackageController's tier check. Lets a tenant plug in
// their own WhatsApp Business Cloud API credentials for the AI lead-collection bot.
class WhatsAppSettingController extends Controller
{
    public function edit()
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin->isAdmin()) {
            abort(403);
        }

        $setting = WhatsAppSetting::forCurrentTenant();

        return view('admin.whatsapp.settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'phone_number_id' => 'nullable|string|max:255',
            'access_token' => 'nullable|string',
            'business_account_id' => 'nullable|string|max:255',
            'enabled' => 'sometimes|boolean',
        ]);

        $setting = WhatsAppSetting::forCurrentTenant();

        // A blank access_token field means "leave it unchanged" — the input is masked
        // in the view and it would otherwise wipe out a saved token on every save.
        if (($data['access_token'] ?? '') === '') {
            unset($data['access_token']);
        }

        $data['enabled'] = $request->boolean('enabled');

        $setting->update($data);

        return redirect()
            ->route('crm.whatsapp-settings.edit')
            ->with('success', 'WhatsApp Bot settings updated.');
    }
}
