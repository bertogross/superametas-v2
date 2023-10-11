<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostMeta;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('meta')->paginate(10);
        return view('posts.index', compact('posts'));
    }

    public function show($id)
    {
        $post = Post::with('meta')->findOrFail($id);
        return view('posts.show', compact('post'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'post_title' => 'required|max:191',
            'post_content' => 'required',
            // Add other validation rules as needed
        ]);

        // Create the post
        $post = Post::create($request->all());

        // Handle post meta
        if($request->has('meta')) {
            foreach($request->meta as $key => $value) {
                $post->meta()->create([
                    'meta_key' => $key,
                    'meta_value' => $value,
                ]);
            }
        }

        return redirect()->route('posts.index')->with('success', 'Post created successfully!');
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'post_title' => 'required|max:191',
            'post_content' => 'required',
            // Add other validation rules as needed
        ]);

        // Update the post
        $post = Post::findOrFail($id);
        $post->update($request->all());

        // Handle post meta
        if($request->has('meta')) {
            foreach($request->meta as $key => $value) {
                $meta = $post->meta()->updateOrCreate(
                    ['meta_key' => $key],
                    ['meta_value' => $value]
                );
            }
        }

        return redirect()->route('posts.index')->with('success', 'Post updated successfully!');
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully!');
    }
}
