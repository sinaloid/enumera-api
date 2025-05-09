<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckInternalApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-Internal-Api-Key');
        $validApiKey = env('INTERNAL_API_KEY'); // clé stockée dans .env

        if (!$apiKey || $apiKey !== $validApiKey) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
