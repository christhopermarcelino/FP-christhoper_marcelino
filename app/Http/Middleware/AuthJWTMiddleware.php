<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthJWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = [
            'status' => 'failed',
        ];

        try {
            // Validate the token
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            $response['message'] = 'Token has expired';
            return response()->json($response, 401);
        } catch (TokenInvalidException $e) {
            $response['message'] = 'Token is invalid';
            return response()->json($response, 401);
        } catch (JWTException $e) {
            $response['message'] = 'Token is not provided';
            return response()->json($response, 401);
        }
    
        return $next($request);
    }
}
