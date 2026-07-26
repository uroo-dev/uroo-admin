<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        return view('projects.index');
    }

    public function store(ProjectRequest $request)
    {
        auth()->user()->projects()->create($request->validated());

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $this->authorize('update', $project);
        $project->update($request->validated());

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
