<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowFrontendRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->header('Origin');
        $allowedOrigins = array_filter([
            'http://127.0.0.1:8000',
            'http://localhost:8000',
            'http://127.0.0.1:5173',
            'http://localhost:5173',
            env('FRONTEND_URL'),
        ]);

        if ($request->isMethod('OPTIONS')) {
            $response = response('', 204);
        } else {
            $response = $next($request);
        }

        if ($origin && in_array($origin, $allowedOrigins, true)) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        } else {
            $response->headers->set('Access-Control-Allow-Origin', 'http://127.0.0.1:8000');
        }

        return $response
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, X-Requested-With, Authorization')
            ->header('Access-Control-Allow-Credentials', 'true');
    }
}
