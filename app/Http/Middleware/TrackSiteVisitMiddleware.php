<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackSiteVisitMiddleware
{
    /**
     * Handle an incoming request and log site visits.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET') && !$request->ajax() && !$request->pjax()) {
            $path = trim($request->path(), '/');

            // Exclude admin, system, livewire, debug, static files
            $excludedPrefixes = [
                'admin',
                'livewire',
                '_debugbar',
                'telescope',
                'storage',
                'assets',
                'build',
                'sanctum',
                'api/v1/health',
            ];

            foreach ($excludedPrefixes as $prefix) {
                if (str_starts_with($path, $prefix)) {
                    return $response;
                }
            }

            try {
                $ip = $request->ip() ?? '127.0.0.1';
                $cacheKey = "site_visit_" . md5($ip);

                if (!Cache::has($cacheKey)) {
                    Cache::put($cacheKey, true, now()->addHours(2));

                    $user = auth('sanctum')->user() ?? auth()->user();
                    $visitorName = $user ? "العميل [{$user->name}]" : "زائر جديد [{$ip}]";
                    $targetPath = $path ?: 'الصفحة الرئيسية';

                    ActivityLogger::log(
                        event: 'site_visit',
                        description: "قام {$visitorName} بزيارة المتجر عبر: [/{$targetPath}]",
                        subjectType: 'Site',
                        subjectId: null,
                        newValues: [
                            'path' => $path,
                            'ip'   => $ip,
                            'referrer' => $request->header('referer'),
                        ],
                        userId: $user?->id,
                        userName: $user?->name ?? "زائر ({$ip})"
                    );
                }
            } catch (\Throwable $e) {
                // Silently continue
            }
        }

        return $response;
    }
}

