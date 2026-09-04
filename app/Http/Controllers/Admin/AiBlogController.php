<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AiGenerationException;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Services\AiBlogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AiBlogController extends Controller
{
    public function generate(Request $request, AiBlogService $ai)
    {
        $data = $request->validate([
            'topic' => 'required|string|max:1000',
            'category' => 'nullable|in:'.implode(',', array_keys(Blog::CATEGORIES)),
            'tone' => 'nullable|string|max:255',
        ]);

        $adminId = Auth::guard('admin')->id();
        $limiterKey = "ai-generate-blog:{$adminId}";
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

        $imageBase64 = $ai->generateImage($draft['title'] ?? $data['topic'], $draft['excerpt'] ?? '');

        return response()->json([
            'success' => true,
            'data' => $draft,
            'image_base64' => $imageBase64,
        ]);
    }
}
