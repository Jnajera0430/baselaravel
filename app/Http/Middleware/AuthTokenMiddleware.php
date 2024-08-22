<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Response;

class AuthTokenMiddleware
{
  public function handle(Request $request, Closure $next, ...$guards)
  {
    $token = $request->header('Authorization');

    if (!$token) {
      return response()->json([
        'message' => 'Token no proporcionado',
        'status' => 401,
      ], 401);
    }

    try {
      $token = str_replace('Bearer ', '', $token);

      $user = JWTAuth::setToken($token)->authenticate();
      // return response()->json([
      //   'message' => $user,
      //   "token" => $token
      // ]);
    } catch (JWTException $e) {
      return response()->json([
        'message' => 'Token no válido',
        'status' => 401,
      ], 401);
    }

    if (!$user) {
      return response()->json([
        'message' => 'Token no válido',
        'status' => 401,
      ], 401);
    }

    $request->setUserResolver(function () use ($user) {
      return $user;
    });

    return $next($request);
  }
}
