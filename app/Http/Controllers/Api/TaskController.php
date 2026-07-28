<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Category $category)
    {
        //
        return response()->json([
            'tasks' => $category->tasks()->orderBy('position')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Category $category)
    {
        //
        $next = Task::max('position') + 1;

        $validatedData = $request->validate([
            'title' => 'required',
            'assigned_to' => 'required',
        ]);

        $validatedData['user_id'] = auth()->id();
        $validatedData["position"] = $next;

        $task = $category->tasks()->create($validatedData);
        return response()->json([
            'task' => $task,
            'message' => 'Task created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
