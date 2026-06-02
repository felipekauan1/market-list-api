<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token não enviado.'], 401);
        }

        $response = Http::withToken($token)->get('http://localhost:8000/api/validate-token');

        if ($response->successful()) {
            $request->merge(['user_id' => $response->json('usuario.id')]);
            return $next($request);
        }

        return response()->json(['message' => 'Token inválido.'], 401);
    }
}
