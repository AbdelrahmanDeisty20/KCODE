<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBlogAuthor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->type, ['blog_authors', 'blog_author', 'admin', 'super_admin'])) {
            return response()->json([
                'status' => false,
                'message' => __('messages.unauthorized_blog_author'),
            ], 403);
        }

        return $next($request);
    }
}
