<?php

namespace App\Http\Controllers;

use App\Models\SurveyExecution;
use Illuminate\Http\Request;

class SurveyExecutionController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $surveyExecutions = SurveyExecution::all();
        return view('survey_executions.index', compact('surveyExecutions'));
    }

    // Show the form for creating a new resource.
    public function create()
    {
        // Return view to create a new survey execution
        return view('survey_executions.create');
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'survey_id' => 'required|exists:surveys,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:not_started,claimed,in_progress,completed,abandoned',
            // Add other fields and validation rules as needed
        ]);

        $surveyExecution = SurveyExecution::create($validatedData);

        return redirect()->route('survey-executions.show', $surveyExecution)
                         ->with('success', 'Survey execution created successfully.');
    }

    // Display the specified resource.
    public function show(SurveyExecution $surveyExecution)
    {
        return view('survey_executions.show', compact('surveyExecution'));
    }

    // Show the form for editing the specified resource.
    public function edit(SurveyExecution $surveyExecution)
    {
        return view('survey_executions.edit', compact('surveyExecution'));
    }

    // Update the specified resource in storage.
    public function update(Request $request, SurveyExecution $surveyExecution)
    {
        $validatedData = $request->validate([
            'survey_id' => 'required|exists:surveys,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:not_started,claimed,in_progress,completed,abandoned',
            // Add other fields and validation rules as needed
        ]);

        $surveyExecution->update($validatedData);

        return redirect()->route('survey-executions.show', $surveyExecution)
                         ->with('success', 'Survey execution updated successfully.');
    }

    // Remove the specified resource from storage.
    public function destroy(SurveyExecution $surveyExecution)
    {
        $surveyExecution->delete();

        return redirect()->route('survey-executions.index')
                         ->with('success', 'Survey execution deleted successfully.');
    }

    // Additional methods can be added as needed...
}
