<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $query = Bookmark::where('user_id', $userId);
        $search = $request->input('search', '');
        $category = $request->input('category', '');
        $showFavorites = $request->boolean('favorites');

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

        $categories = Bookmark::where('user_id', $userId)
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $totalCount = Bookmark::where('user_id', $userId)->count();
        $favoritesCount = Bookmark::where('user_id', $userId)->where('is_favorite', true)->count();

        return view('bookmarks.index', compact(
            'bookmarks', 'categories', 'search', 'category',
            'showFavorites', 'totalCount', 'favoritesCount'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'url'        => 'required|url|max:2048',
            'description'=> 'nullable|string|max:1000',
            'category'   => 'nullable|string|max:100',
            'tags_input' => 'nullable|string|max:500',
            'logo_url'   => 'nullable|url|max:2048',
            'is_favorite'=> 'nullable|boolean',
        ]);

        $data['tags'] = $this->parseTags($data['tags_input'] ?? '');
        $data['is_favorite'] = $request->boolean('is_favorite');
        unset($data['tags_input']);

        auth()->user()->bookmarks()->create($data);

        return redirect()->route('bookmarks.index')->with('success', 'Bookmark saved successfully.');
    }

    public function update(Request $request, Bookmark $bookmark)
    {
        $this->authorize('update', $bookmark);

        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'url'        => 'required|url|max:2048',
            'description'=> 'nullable|string|max:1000',
            'category'   => 'nullable|string|max:100',
            'tags_input' => 'nullable|string|max:500',
            'logo_url'   => 'nullable|url|max:2048',
            'is_favorite'=> 'nullable|boolean',
        ]);

        $data['tags'] = $this->parseTags($data['tags_input'] ?? '');
        $data['is_favorite'] = $request->boolean('is_favorite');
        unset($data['tags_input']);

        $bookmark->update($data);

        return redirect()->route('bookmarks.index')->with('success', 'Bookmark updated successfully.');
    }

    public function destroy(Bookmark $bookmark)
    {
        $this->authorize('delete', $bookmark);
        $bookmark->delete();

        return redirect()->route('bookmarks.index')->with('success', 'Bookmark deleted.');
    }

    public function toggleFavorite(Bookmark $bookmark)
    {
        $this->authorize('update', $bookmark);
        $bookmark->update(['is_favorite' => ! $bookmark->is_favorite]);

        return redirect()->route('bookmarks.index');
    }

    // ── Private helpers ──────────────────────────────────────────

    private function parseTags(?string $raw): array
    {
        if (empty($raw)) {
            return [];
        }

        return array_values(
            array_filter(
                array_map('trim', explode(',', $raw))
            )
        );
    }
}
