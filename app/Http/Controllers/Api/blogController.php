<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Facades\Validator;

class blogController extends Controller
{
  public function getListBlogs(Request $request)
  {
    $user = $request->user();
    $blogs = Blog::with('user')->get();

    $formattedBlogs = $blogs->map(function ($blog) {
      return [
        "id" => $blog->id,
        "title" => $blog->title,
        "author" => $blog->author,
        "url" => $blog->url,
        "likes" => $blog->likes,
        "user" => [
          "id" => $blog->user->id,
          "name" => $blog->user->name,
          "username" => $blog->user->username,
        ]
      ];
    });
    if ($blogs->isEmpty()) {
      $data = [
        "message" => 'No se encontraron datos de los blogs',
        "status" => 404,
        "data" => $formattedBlogs,
      ];
      return response()->json($data);
    };

    $data = [
      "message" => 'Listados de blogs exitosa.',
      "status" => 200,
      "data" => $formattedBlogs
    ];
    return response()->json($formattedBlogs);
  }

  public function createBlog(Request $request)
  {
    $user = $request->user();
    $validator = Validator::make($request->all(), [
      "title" => "required",
      "author" => "required",
      "url" => "required",
      "likes" => "required",
    ]);

    if ($validator->fails()) {
      $data = [
        "message" => 'Por favor ingresa todos los datos requeridos',
        "errors" => $validator->errors(),
        "status" => 400,
      ];
      return response()->json($data, 400);
    }

    $blog = $user->blogs()->create([
      'title' => $request->title,
      'author' => $request->author,
      'url' => $request->url,
      'likes' => $request->likes,
    ]);

    if (!$blog) {
      $data = [
        "message" => "Ocurrió un error al crear el blog.",
        "status" => 500
      ];

      return response()->json($data, 500);
    }

    $data = [
      "message" => "Blog creado con exito",
      "status" => 201,
      "data" => $blog,
      "user" => $user
    ];

    return response()->json($data, 201);
  }

  public function getOneBlog($id)
  {
    $blog = Blog::with(['user' => function ($query) {
      $query->select('id', 'name', 'username');
    }])->find($id);

    $formattedBlog =  [
      "title" => $blog->title,
      "author" => $blog->author,
      "url" => $blog->url,
      "user" => [
        "id" => $blog->user->id,
        "name" => $blog->user->name,
        "username" => $blog->user->username,
      ]
    ];

    if (!$blog) {
      $data = [
        "message" => "El blog no fue encontrado",
        "status" => 404,
      ];

      return response()->json($data, 404);
    }

    $data = [
      "message" => "El blog fue encontrado con exito",
      "status" => 200,
      "data" => $formattedBlog
    ];

    return response()->json($formattedBlog, 200);
  }

  public function deleteOneBlog($id)
  {
    $blog = Blog::find($id);
    if (!$blog) {
      $data = [
        "message" => "El blog no fue encontrado",
        "status" => 404,
      ];
      return response()->json($data, 404);
    }

    $blog->delete();

    $data = [
      "message" => "El blog ha sido eliminado con exito.",
      "status" => 200
    ];

    return response()->json($data, 200);
  }

  public function editOneBlog(Request $request, $id)
  {
    $blog = Blog::find($id);
    if (!$blog) {
      $data = [
        "message" => "El blog no fue encontrado",
        "status" => 404,
      ];
      return response()->json($data, 404);
    }

    $validator = Validator::make($request->all(), [
      "title" => "required",
      "author" => "required",
      "url" => "required",
      "likes" => "required",
    ]);

    if ($validator->fails()) {
      $data = [
        "message" => 'Por favor ingresa todos los datos requeridos',
        "errors" => $validator->errors(),
        "status" => 400,
      ];
      return response()->json($data, 400);
    }

    $blog->title = $request->title;
    $blog->author = $request->author;
    $blog->url = $request->url;
    $blog->likes = $request->likes;

    $blog->save();

    $data = [
      "message" => "Blog editado con exito.",
      "status" => 200,
      "data" => $blog
    ];

    return response()->json($blog, 200);
  }

  public function editPartialOneBlog(Request $request, $id)
  {
    $blog = Blog::find($id);
    if (!$blog) {
      $data = [
        "message" => "El blog no fue encontrado",
        "status" => 404,
      ];
      return response()->json($data, 404);
    }

    $validator = Validator::make($request->all(), [
      "title" => "sometimes|required",
      "author" => "sometimes|required",
      "url" => "sometimes|required|url",
      "likes" => "sometimes|required|integer",
    ]);

    if ($validator->fails()) {
      $data = [
        "message" => 'Por favor ingresa todos los datos requeridos',
        "errors" => $validator->errors(),
        "status" => 400,
      ];
      return response()->json($data, 400);
    }

    if ($request->has("title")) {
      $blog->title = $request->title;
    }
    if ($request->has("author")) {
      $blog->author = $request->author;
    }
    if ($request->has("url")) {
      $blog->url = $request->url;
    }
    if ($request->has("likes")) {
      $blog->likes = $request->likes;
    }

    $blog->save();

    $data = [
      "message" => "Blog editado con exito.",
      "status" => 200,
      "data" => $blog
    ];

    return response()->json($blog, 200);
  }
}
