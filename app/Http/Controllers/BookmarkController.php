<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index(Request $request)
    {
        $query = Bookmark::query();
        $search = $request->input('search', '');
        $category = $request->input('category', '');
        $showFavorites = $request->boolean('favorites');
        $viewMode = $request->input('view', 'grid');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($category) {
            $query->where('category', $category);
        }
        if ($showFavorites) {
            $query->where('is_favorite', true);
        }

        $bookmarks = $query->orderBy('is_favorite', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(24)
            ->appends($request->query());

        $categories = Bookmark::select('category')->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        return view('bookmarks.index', compact('bookmarks', 'categories', 'search', 'category', 'showFavorites', 'viewMode'));
    }

    public function store(Request $request)
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

    public function update(Request $request, Bookmark $bookmark)
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

    public function toggleFavorite(Bookmark $bookmark)
    {
        $this->authorize('update', $bookmark);
        $bookmark->update(['is_favorite' => !$bookmark->is_favorite]);
        return redirect()->route('bookmarks.index');
    }
}
