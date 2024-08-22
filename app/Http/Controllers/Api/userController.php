<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class userController extends Controller
{
  public function getAllUsers()
  {
    $users = User::with('blogs')->get();
    if ($users->isEmpty()) {
      $data = [
        "message" => 'No se encontraron datos de los usuarios',
        "status" => 404,
        "data" => $users
      ];
      return response()->json($data);
    };

    $formattedUsers = $users->map(function ($user) {
      return [
        "username" => $user->username,
        "name" => $user->name,
        "blogs" => $user->blogs->map(function ($blog) {
          return [
            "title" => $blog->title,
            "author" => $blog->author,
            "url" => $blog->url,
            "likes" => $blog->likes
          ];
        })
      ];
    });

    $data = [
      "message" => 'Listados de usuario exitoso.',
      "status" => 200,
      "data" => $formattedUsers
    ];
    return response()->json($data);
  }

  public function createUser(Request $request)
  {
    $validator = Validator::make($request->all(), [
      "name" => "required",
      "username" => "required",
      "password" => "required",
    ]);

    if ($validator->fails()) {
      $data = [
        "message" => 'Por favor ingresa todos los datos requeridos',
        "errors" => $validator->errors(),
        "status" => 400,
      ];
      return response()->json($data, 400);
    }
    $passwordHarcode = Hash::make($request->password);
    $user = User::create([
      "name" => $request->name,
      "username" => $request->username,
      "password" => $passwordHarcode,
    ]);

    if (!$user) {
      $data = [
        "message" => "Ocurrió un error al crear el usuario.",
        "status" => 500
      ];

      return response()->json($data, 500);
    }

    $data = [
      "message" => "Usuario creado con exito",
      "status" => 201,
      "data" => $user
    ];

    return response()->json($data, 201);
  }
}
