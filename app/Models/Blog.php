<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
  use HasFactory;

  protected $table = "blogs";

  protected $hidden = [
    "created_at",
    "updated_at"
  ];

  protected $fillable = [
    "title",
    "author",
    "url",
    "likes"
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
