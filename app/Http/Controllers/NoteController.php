<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Services\NoteService;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Note::query();
        $search = $request->input('search', '');
        $category = $request->input('category', '');
        $showFavorites = $request->boolean('favorites');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }
        if ($category) {
            $query->where('category', $category);
        }
        if ($showFavorites) {
            $query->where('is_favorite', true);
        }

        $notes = $query->orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(12)
            ->appends($request->query());

        $categories = Note::select('category')->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        return view('notes.index', compact('notes', 'categories', 'search', 'category', 'showFavorites'));
    }

    public function store(Request $request, NoteService $noteService)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|string',
            'is_pinned' => 'boolean',
            'is_favorite' => 'boolean',
        ]);
        $data['tags'] = $noteService->parseTags($data['tags'] ?? '');
        auth()->user()->notes()->create($data);

        return redirect()->route('notes.index')->with('success', 'Note created successfully.');
    }

    public function update(Request $request, Note $note, NoteService $noteService)
    {
        $this->authorize('update', $note);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|string',
            'is_pinned' => 'boolean',
            'is_favorite' => 'boolean',
        ]);
        $data['tags'] = $noteService->parseTags($data['tags'] ?? '');
        $note->update($data);

        return redirect()->route('notes.index')->with('success', 'Note updated successfully.');
    }

    public function destroy(Note $note)
    {
        $this->authorize('delete', $note);
        $note->delete();

        return redirect()->route('notes.index')->with('success', 'Note deleted successfully.');
    }

    public function togglePin(Note $note)
    {
        $this->authorize('update', $note);
        $note->update(['is_pinned' => ! $note->is_pinned]);

        return redirect()->route('notes.index');
    }

    public function toggleFavorite(Note $note)
    {
        $this->authorize('update', $note);
        $note->update(['is_favorite' => ! $note->is_favorite]);

        return redirect()->route('notes.index');
    }
}
