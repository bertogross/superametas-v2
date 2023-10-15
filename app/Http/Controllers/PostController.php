<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostMeta;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // Specify the database connection to be used for this model
    protected $connection = 'smAppTemplate';


    /**
     * Display a listing of the posts.
     *
     * Retrieve all posts with their associated meta data, paginating the results
     * to display 10 posts per page, and pass them to the 'posts.index' view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $posts = Post::on('smAppTemplate')->with('meta')->paginate(10);
        return view('posts.index', compact('posts'));
    }

    /**
     * Display the specified post.
     *
     * Retrieve the post with the given ID along with its meta data,
     * and pass it to the 'posts.show' view. If the post cannot be found,
     * a 404 HTTP response is automatically sent.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $post = Post::on('smAppTemplate')->with('meta')->findOrFail($id);
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for creating a new post.
     *
     * Return the 'posts.create' view where the user can enter details
     * for a new post.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created post in storage.
     *
     * Validate the incoming request data, create a new post record,
     * handle the creation of associated meta data, and redirect the user
     * back to the posts index with a success message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'post_title' => 'required|max:191',
            'post_content' => 'required',
            'post_type' => 'required',
            'emp' => 'required',
            'year' => 'required',
            'month' => 'required',
            'goals' => 'required',
        ]);

        // Create the post
        $post = Post::on('smAppTemplate')->create($request->only(['post_title', 'post_content', 'post_type']));

        // Metas from form
        // emp, year, month, goals
        $metaKeys = ['emp', 'year', 'month', 'goals'];
        foreach($metaKeys as $key) {
            if($request->has($key)) {
                $value = $request->input($key);

                // If the key is 'goals', encode the value as JSON
                if($key === 'goals' && is_array($value)) {
                    $value = json_encode($value);
                }

                $post->meta()->create([
                    'meta_key' => $key,
                    'meta_value' => $value,
                ]);
            }
        }

        return redirect()->route('posts.index')->with('success', 'Post created successfully!');
    }


    /**
     * Show the form for editing the specified post.
     *
     * Retrieve the post with the given ID and pass it to the 'posts.edit' view.
     * If the post cannot be found, a 404 HTTP response is automatically sent.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $post = Post::on('smAppTemplate')->findOrFail($id);
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified post in storage.
     *
     * Validate the incoming request data, update the specified post record,
     * handle the updating/creation of associated meta data, and redirect the user
     * back to the posts index with a success message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'post_title' => 'required|max:191',
            'post_content' => 'required',
            'emp' => 'required',
            'year' => 'required',
            'month' => 'required',
            'goals' => 'required',
        ]);

        $post = Post::on('smAppTemplate')->findOrFail($id);
        $post->update($request->only(['post_title', 'post_content']));

        // Metas from form
        // emp, year, month, goals
        $metaKeys = ['emp', 'year', 'month', 'goals'];
        foreach($metaKeys as $key) {
            if($request->has($key)) {
                $value = $request->input($key);

                // If the key is 'goals', ensure the value is a JSON string
                if($key === 'goals' && is_array($value)) {
                    $value = json_encode($value);
                }

                $post->meta()->updateOrCreate(
                    ['meta_key' => $key],
                    ['meta_value' => $value]
                );
            }
        }

        return redirect()->route('posts.index')->with('success', 'Post updated successfully!');
    }


    /**
     * Remove the specified post from storage.
     *
     * Retrieve the post with the given ID and delete it. Then, redirect the user
     * back to the posts index with a success message. If the post cannot be found,
     * a 404 HTTP response is automatically sent.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $post = Post::on('smAppTemplate')->findOrFail($id);
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully!');
    }


    public function showGoalSales()
    {
        $posts = Post::on('smAppTemplate')->where('post_type', 'goal-sales')->with('meta')->get();//->paginate(10)
        return view('goal-sales', compact('posts'));
    }

    public function showGoalResults()
    {
        $posts = Post::on('smAppTemplate')->where('post_type', 'goal-results')->with('meta')->get();//->paginate(10)
        return view('goal-results', compact('posts'));
    }

    /*
    public function showGoalSales($startYear = null, $endYear = null, $startMonth = null, $endMonth = null)
    {
        // default values if parameters are null
        $startYear = $startYear ?? date('Y');
        $endYear = $endYear ?? date('Y');
        $startMonth = $startMonth ?? date('m');
        $endMonth = $endMonth ?? date('m');

        $posts = DB::connection('smAppTemplate')
            ->table('posts')
            ->join('post_metas', 'posts.id', '=', 'post_metas.post_id')
            ->where('posts.post_type', 'goal-sales')
            ->where(function ($query) use ($startYear, $endYear, $startMonth, $endMonth) {
                $query->whereRaw('JSON_VALID(post_metas.meta_value)')
                    ->whereBetween('post_metas.meta_value->year', [(string) $startYear, (string) $endYear])
                    ->whereBetween('post_metas.meta_value->month', [(string) $startMonth, (string) $endMonth]);
            })
            ->select('posts.*')
            ->paginate(10);

        return view('goal-sales', compact('posts'));
    }


    public function showGoalResults($startYear = null, $endYear = null, $startMonth = null, $endMonth = null)
    {
        // default values if parameters are null
        $startYear = $startYear ?? date('Y');
        $endYear = $endYear ?? date('Y');
        $startMonth = $startMonth ?? date('m');
        $endMonth = $endMonth ?? date('m');

        $posts = DB::connection('smAppTemplate')
            ->table('posts')
            ->join('post_metas', 'posts.id', '=', 'post_metas.post_id')
            ->where('posts.post_type', 'goal-results')
            ->where(function ($query) use ($startYear, $endYear, $startMonth, $endMonth) {
                $query->whereRaw('JSON_VALID(post_metas.meta_value)')
                    ->whereBetween('post_metas.meta_value->year', [(string) $startYear, (string) $endYear])
                    ->whereBetween('post_metas.meta_value->month', [(string) $startMonth, (string) $endMonth]);
            })
            ->select('posts.*')
            ->paginate(10);

        return view('goal-results', compact('posts'));
    }
    */
}
