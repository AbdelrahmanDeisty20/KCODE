<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBlogAuthor
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->type, ['blog_authors', 'blog_aouhtros', 'admin', 'super_admin'])) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: User must have type blog_authors to perform blog operations.',
            ], 403);
        }

        return $next($request);
    }
}
