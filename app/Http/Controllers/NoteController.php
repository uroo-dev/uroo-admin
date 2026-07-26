<?php

namespace App\Http\Controllers;

use App\Models\Note;

class NoteController extends Controller
{
    public function index()
    {
        return view('notes.index');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'is_pinned' => 'boolean',
            'is_favorite' => 'boolean',
        ]);

        auth()->user()->notes()->create($data);

        return redirect()->route('notes.index')->with('success', 'Note created successfully.');
    }

    public function update(\Illuminate\Http\Request $request, Note $note)
    {
        $this->authorize('update', $note);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'is_pinned' => 'boolean',
            'is_favorite' => 'boolean',
        ]);

        $note->update($data);

        return redirect()->route('notes.index')->with('success', 'Note updated successfully.');
    }

    public function destroy(Note $note)
    {
        $this->authorize('delete', $note);
        $note->delete();

        return redirect()->route('notes.index')->with('success', 'Note deleted successfully.');
    }
}
