<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function createPost(Request $request){
        $incomingFields = $request->validate([
            'post_tile' => 'required',
            'post_content' => 'required'
        ]);

        $incomingFields['post_tile'] = strip_tags($incomingFields['post_tile']);
        $incomingFields['post_content'] = strip_tags($incomingFields['post_content']);
        $incomingFields['post_author'] = auth()->id();

        Post::create($incomingFields);
        return redirect('/');
    }
}
