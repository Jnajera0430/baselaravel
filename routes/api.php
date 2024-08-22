<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\blogController;
use App\Http\Controllers\Api\userController;
use App\Http\Controllers\Api\authController;
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


//auth
Route::post("/login", [authController::class, "login"]);

Route::middleware([\App\Http\Middleware\AuthTokenMiddleware::class])->group(function () {
  //users
  Route::get("/users", [userController::class, "getAllUsers"]);
  Route::post("/users", [userController::class, "createUser"]);

  //blogs
  Route::get("/blogs", [blogController::class, "getListBlogs"]);
  Route::post("/blogs", [blogController::class, "createBlog"]);
  Route::get("/blogs/{id}", [blogController::class, "getOneBlog"]);
  Route::delete("/blogs/{id}", [blogController::class, "deleteOneBlog"]);
  Route::put("/blogs/{id}", [blogController::class, "editOneBlog"]);
  Route::patch("/blogs/{id}", [blogController::class, "editPartialOneBlog"]);
});
