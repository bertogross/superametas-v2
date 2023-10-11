<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['post_title', 'post_content'];

    public function meta()
    {
        return $this->hasMany(PostMeta::class);
    }
}
