<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;

class BookmarkController extends Controller
{
    public function index()
    {
        return view('bookmarks.index');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'description' => 'nullable|string|max:1000',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'logo_url' => 'nullable|url|max:2048',
            'is_favorite' => 'boolean',
        ]);

        auth()->user()->bookmarks()->create($data);

        return redirect()->route('bookmarks.index')->with('success', 'Bookmark created successfully.');
    }

    public function update(\Illuminate\Http\Request $request, Bookmark $bookmark)
    {
        $this->authorize('update', $bookmark);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:2048',
            'description' => 'nullable|string|max:1000',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'logo_url' => 'nullable|url|max:2048',
            'is_favorite' => 'boolean',
        ]);

        $bookmark->update($data);

        return redirect()->route('bookmarks.index')->with('success', 'Bookmark updated successfully.');
    }

    public function destroy(Bookmark $bookmark)
    {
        $this->authorize('delete', $bookmark);
        $bookmark->delete();

        return redirect()->route('bookmarks.index')->with('success', 'Bookmark deleted successfully.');
    }
}
