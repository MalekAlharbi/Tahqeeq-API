<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Project;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    // Get All categories in project
    public function index(Project $project)
    {
        //
        $categories = $project->categories()
            ->orderBy('position')
            ->with(['tasks' => fn($query) => $query->orderBy('position')])
            ->get();

        return response()->json([
            'categories' => CategoryResource::collection($categories),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Project $project)
    {
        //
        $next = Category::max('position') + 1;

        $validatedData = $request->validate([
            'title' => 'required',
        ]);

        $validatedData["user_id"] = auth()->id();
        $validatedData["position"] = $next;


        $project->categories()->create($validatedData);

        return response()->json([
            'message' => 'Category created'
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
    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'position' => 'nullable',
        ]);

        $oldPosition = $category->position;
        $newPosition = $validatedData['position'];

        if ($newPosition !== null && $oldPosition !== $newPosition) {
            if ($newPosition < $oldPosition) {
                Category::where('position', '>=', $newPosition)
                    ->where('position', '<', $oldPosition)
                    ->increment('position');
            } else {
                Category::where('position', '>', $oldPosition)
                    ->where('position', '<=', $newPosition)
                    ->decrement('position');
            }
        }

        $category->update($validatedData);

        return response()->json([
            'message' => 'Category updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
        $category->delete();

        Category::where('position','>',$category->position)->decrement('position');

        return response()->json([
            'message' => 'Category deleted'
        ]);
    }
}
