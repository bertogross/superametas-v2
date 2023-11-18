<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SurveyTerms;
use Illuminate\Support\Str;

class SurveyTermsController extends Controller
{
    protected $connection = 'smAppTemplate';

    public function index()
    {
        $terms = SurveyTerms::all();
        return view('surveys.terms.listing', compact('terms'));
    }

    public function create()
    {
        return view('surveys.terms.create');
    }

    public function show($id = null)
    {
        $term = SurveyTerms::findOrFail($id);

        return view('surveys.terms.show', compact('term'));
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        $validatedData = $request->validate([
            'name' => 'required',
        ]);

        $termName = SurveyTerms::cleanedName($validatedData['name']);

        $existingTerm = SurveyTerms::where('name', $termName)->first();
        if ($existingTerm) {
            // Handle the duplicate name scenario
            return response()->json(['success' => false, 'message' => 'Termo já existe!']);
        }

        $userId = auth()->id();

        $term = new SurveyTerms;
        $term->user_id = $userId;
        $term->name = $termName;
        $term->slug = $this->createUniqueSlug($termName);
        $term->save();

        return response()->json(['success' => true, 'term' => $term, 'message' => 'Termo registrado!']);
    }

    public function createUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = SurveyTerms::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    public function search(Request $request)
    {
        // Search for terms based on the query
        $searchQuery = $request->input('query');
        $terms = SurveyTerms::where('name', 'LIKE', "%{$searchQuery}%")->get();

        return $terms ? response()->json($terms) : null;
    }

    public function form()
    {
        $terms = SurveyTerms::all();

        return view('surveys.terms.form', compact('terms'));
    }

}
