<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SurveyTerm;
use Illuminate\Support\Str;

class SurveyTermController extends Controller
{

    public function index()
    {
        $terms = SurveyTerm::all();
        return view('surveys.terms.listing', compact('terms'));
    }

    public function create()
    {
        return view('surveys.terms.create');
    }

    public function show($id = null)
    {
        $term = SurveyTerm::findOrFail($id);

        return view('surveys.terms.show', compact('term'));
    }

    public function storeOrUpdate(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:terms,name',
        ]);

        $term = new Term;
        $term->name = $validatedData['name'];
        $term->slug = $this->createUniqueSlug($validatedData['name']);
        $term->save();

        return response()->json(['success' => true, 'term' => $term, 'message' => $message]);

        /*
        // Check if the term already exists
        $term = SurveyTerm::where('name', $validatedData['name'])->first();

        // If the term doesn't exist, create it
        if (!$term) {
            // Generate a slug from the name
            $slug = Str::slug($validatedData['name']);

            // Create the term with the unique slug
            $term = SurveyTerm::create([
                'name' => $validatedData['name'],
                'slug' => $slug,
            ]);
            $message = 'Term created successfully.';

            return response()->json(['success' => true, 'term' => $term, 'message' => $message]);

        } else {
            $message = 'Term already exists.';

            return response()->json(['success' => false, 'term' => $term, 'message' => $message]);
        }
        */
    }

    public function createUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = SurveyTerm::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    public function search(Request $request)
    {
        // Search for terms based on the query
        $searchQuery = $request->input('query');
        $terms = SurveyTerm::where('name', 'LIKE', "%{$searchQuery}%")->get();

        return $terms ? response()->json($terms) : null;
    }


}
