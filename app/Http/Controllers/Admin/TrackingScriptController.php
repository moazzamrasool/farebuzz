<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TrackingScriptRequest;
use App\Models\TrackingScript;
use Illuminate\Support\Facades\Auth;

// Company-owner-only — mirrors WhatsAppSettingController's tier check. Lets a tenant
// paste raw analytics/tracking code (GA4, GTM, Facebook Pixel, ...) into every
// frontend page without touching a physical file.
class TrackingScriptController extends Controller
{
    public function edit()
    {
        if (!Auth::guard('admin')->user()->isAdmin()) {
            abort(403);
        }

        $tracking = TrackingScript::forCurrentTenant();

        return view('admin.tracking.edit', compact('tracking'));
    }

    public function update(TrackingScriptRequest $request)
    {
        $tracking = TrackingScript::forCurrentTenant();

        $tracking->update([
            'header_script' => $request->input('header_script'),
            'header_enabled' => $request->boolean('header_enabled'),
            'body_script' => $request->input('body_script'),
            'body_enabled' => $request->boolean('body_enabled'),
            'footer_script' => $request->input('footer_script'),
            'footer_enabled' => $request->boolean('footer_enabled'),
            'updated_by' => Auth::guard('admin')->id(),
        ]);

        return redirect()
            ->route('crm.tracking-scripts.edit')
            ->with('success', 'Tracking scripts updated.');
    }
}
