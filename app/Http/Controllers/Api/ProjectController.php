<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = auth()->user()->projects;
        return response()->json([
            'projects' => $projects
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validatedData = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
        ]);

        $project = Project::create($validatedData);
        $project->users()->attach($user);

        return response()->json([
            'message' => 'Project created'
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
        if(!$project){
            return response()->json([
                'message' => 'Project not found'
            ],404);
        };

        return response()->json([
            'project' => $project->load('categories'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */

    // Don't Forget you need to see user's role before giving access for Update & Destroy.
    public function update(Request $request, Project $project)
    {
        //
        $validatedData = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
        ]);

        if(!$project){
            return response()->json([
                'message' => 'Project not found'
            ],404);
        };

        $project->update($validatedData);

        return response()->json([
            'message' => 'Project updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
        if(!$project){
            return response()->json([
                'message' => 'Project not found'
            ],404);
        };

        $project->delete();

        return response()->json([
            'message' => 'Project deleted'
        ]);
    }
}
