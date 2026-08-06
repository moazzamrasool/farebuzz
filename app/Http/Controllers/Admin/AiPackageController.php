<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AiGenerationException;
use App\Http\Controllers\Controller;
use App\Models\AiPackageSetting;
use App\Services\AiPackageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AiPackageController extends Controller
{
    public function generate(Request $request, AiPackageService $ai)
    {
        if (!AiPackageSetting::isEnabledForCurrentTenant()) {
            return response()->json([
                'success' => false,
                'message' => 'AI Generate is currently turned off for your account.',
            ], 422);
        }

        $data = $request->validate([
            'destination' => 'required|string|max:255',
            'nights' => 'required|integer|min:0|max:60',
            'theme' => 'nullable|string|max:255',
            'budget' => 'nullable|string|max:255',
            'traveller_type' => 'nullable|string|max:255',
            'extra_instructions' => 'nullable|string|max:1000',
        ]);

        $adminId = Auth::guard('admin')->id();
        $limiterKey = "ai-generate:{$adminId}";
        $maxAttempts = (int) config('services.ai.rate_limit_per_hour', 20);

        if (RateLimiter::tooManyAttempts($limiterKey, $maxAttempts)) {
            return response()->json([
                'success' => false,
                'message' => 'You have reached the AI generation limit for this hour. Please try again later.',
            ], 429);
        }

        RateLimiter::hit($limiterKey, 3600);

        try {
            $draft = $ai->generate($data);
        } catch (AiGenerationException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'data' => $draft]);
    }

    // Company-owner-only kill switch — mirrors the tier check EnsureHasModulePermission
    // already applies to Roles/Users (an Admin has implicit full control of their own
    // tenant's settings; sub-admins never see this toggle in the UI).
    public function toggleSetting(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin->isAdmin()) {
            abort(403);
        }

        $data = $request->validate(['enabled' => 'required|boolean']);

        $setting = AiPackageSetting::query()->firstOrCreate([]);
        $setting->update(['enabled' => $data['enabled']]);

        return response()->json(['success' => true, 'enabled' => $setting->enabled]);
    }
}
