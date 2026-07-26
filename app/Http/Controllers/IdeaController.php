<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AppIdea;

class IdeaController extends Controller
{
    public function index()
    {
        return view('ideas.index');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'tech_stack' => 'nullable|array',
            'platform' => 'nullable|string|max:100',
            'status' => 'required|in:draft,research,development,archived',
            'priority' => 'required|in:low,medium,high',
            'tags' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        auth()->user()->ideas()->create($data);

        return redirect()->route('ideas.index')->with('success', 'Idea created successfully.');
    }

    public function update(\Illuminate\Http\Request $request, AppIdea $idea)
    {
        $this->authorize('update', $idea);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'tech_stack' => 'nullable|array',
            'platform' => 'nullable|string|max:100',
            'status' => 'required|in:draft,research,development,archived',
            'priority' => 'required|in:low,medium,high',
            'tags' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $idea->update($data);

        return redirect()->route('ideas.index')->with('success', 'Idea updated successfully.');
    }

    public function destroy(AppIdea $idea)
    {
        $this->authorize('delete', $idea);
        $idea->delete();

        return redirect()->route('ideas.index')->with('success', 'Idea deleted successfully.');
    }
}
