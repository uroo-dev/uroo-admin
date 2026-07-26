<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Models\Client;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $service = app(ProjectService::class);
        $search = $request->input('q', '');
        $category = $request->input('category', '');
        $status = $request->input('status', '');
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');

        $stats = $service->getStats();
        $projects = $service->search(
            search: $search,
            category: $category,
            status: $status,
            sortField: $sortField,
            sortDirection: $sortDirection,
            perPage: 10,
        );
        $clients = Client::select('id', 'name')->orderBy('name')->get();

        return view('projects.index', compact('stats', 'projects', 'clients', 'search', 'category', 'status', 'sortField', 'sortDirection'));
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
