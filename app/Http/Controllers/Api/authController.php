<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class authController extends Controller
{
  public function login(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'username' => 'required',
      'password' => 'required',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'Por favor ingresa todos los datos requeridos',
        'errors' => $validator->errors(),
        'status' => 400,
      ], 400);
    }

    $credentials = $request->only('username', 'password');

    if (!$token = JWTAuth::attempt($credentials)) {
      return response()->json([
        'message' => 'Credenciales incorrectas',
        'status' => 401,
      ], 401);
    }

    return $this->respondWithToken($token);
  }

  protected function respondWithToken($token)
  {
    $cookie = cookie("access_token", $token, 60 * 24);
    return response()->json([
      'access_token' => $token,
      'token_type' => 'Bearer',
      'expires_in' => JWTAuth::factory()->getTTL() * 60,
    ])->withoutCookie($cookie);
  }
}
