<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
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
            'tasks' => TaskResource::collection($category->tasks()->orderBy('position')->get()),
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
    public function updateName(Request $request, Task $task)
    {
        //
        $validatedData = $request->validate([
            'title' => 'required',
        ]);

        $task->update($validatedData);

        return response()->json([
            'task' => $task,
            'message' => 'Task updated successfully',
        ]);
    }

    public function updatePosition(Request $request, Task $task)
    {
        //
        $validatedData = $request->validate([
            'category_id' => 'required',
            'position' => 'required',
        ]);

        $oldPosition = $task->position;
        $newPosition = $validatedData['position'];

        if ($newPosition !== null && $oldPosition !== $newPosition) {
            if ($newPosition < $oldPosition) {
                Task::where('position', '>=', $newPosition)
                    ->where('position', '<', $oldPosition)
                    ->increment('position');
            } else {
                Task::where('position', '>', $oldPosition)
                    ->where('position', '<=', $newPosition)
                    ->decrement('position');
            }
        }

        $task->update($validatedData);

        return response()->json([
            'task' => $task,
            'message' => 'Task updated successfully',
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
        $task->delete();

        Category::where('position','>',$task->position)->decrement('position');

        return response()->json([
            'message' => 'Task deleted successfully',
        ]);
    }
}
