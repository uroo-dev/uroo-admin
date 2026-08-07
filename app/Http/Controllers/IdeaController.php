<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AppIdea;
use App\Services\IdeaService;
use Illuminate\Http\Request;

class IdeaController extends Controller
{
    public function index(Request $request)
    {
        $query = AppIdea::where('user_id', auth()->id());
        $search = $request->input('search', '');
        $statusFilter = $request->input('status', '');
        $platformFilter = $request->input('platform', '');
        $sortField = in_array($request->input('sort', 'created_at'), ['name', 'status', 'priority', 'platform', 'created_at']) ? $request->input('sort', 'created_at') : 'created_at';
        $sortDirection = $request->input('direction', 'desc') === 'asc' ? 'asc' : 'desc';

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('tagline', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }
        if ($platformFilter) {
            $query->where('platform', $platformFilter);
        }

        $ideas = $query->orderBy($sortField, $sortDirection)->paginate(12)->appends($request->query());

        $stats = [
            'total' => AppIdea::where('user_id', auth()->id())->count(),
            'draft' => AppIdea::where('user_id', auth()->id())->where('status', 'draft')->count(),
            'development' => AppIdea::where('user_id', auth()->id())->where('status', 'development')->count(),
            'archived' => AppIdea::where('user_id', auth()->id())->where('status', 'archived')->count(),
        ];

        return view('ideas.index', compact('ideas', 'stats', 'search', 'statusFilter', 'platformFilter', 'sortField', 'sortDirection'));
    }

    public function store(Request $request, IdeaService $ideaService)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'tech_stack' => 'nullable|string',
            'platform' => 'nullable|string|max:100',
            'status' => 'required|in:draft,research,development,archived',
            'priority' => 'required|in:low,medium,high',
            'tags' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $data['features'] = $ideaService->parseLines($data['features'] ?? '');
        $data['tech_stack'] = $ideaService->parseLines($data['tech_stack'] ?? '');
        $data['tags'] = $ideaService->parseTags($data['tags'] ?? '');
        auth()->user()->ideas()->create($data);

        return redirect()->route('ideas.index')->with('success', 'Idea created successfully.');
    }

    public function update(Request $request, AppIdea $idea, IdeaService $ideaService)
    {
        $this->authorize('update', $idea);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'tech_stack' => 'nullable|string',
            'platform' => 'nullable|string|max:100',
            'status' => 'required|in:draft,research,development,archived',
            'priority' => 'required|in:low,medium,high',
            'tags' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $data['features'] = $ideaService->parseLines($data['features'] ?? '');
        $data['tech_stack'] = $ideaService->parseLines($data['tech_stack'] ?? '');
        $data['tags'] = $ideaService->parseTags($data['tags'] ?? '');
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
