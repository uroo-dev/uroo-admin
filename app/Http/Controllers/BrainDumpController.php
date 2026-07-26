<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BrainDump;
use Illuminate\Http\Request;

class BrainDumpController extends Controller
{
    public function index(Request $request)
    {
        $query = BrainDump::where('user_id', auth()->id())->where('is_archived', false);
        $search = $request->input('search', '');

        if ($search) {
            $query->where('content', 'like', "%{$search}%");
        }

        $pinnedDumps = (clone $query)->where('is_pinned', true)->orderBy('updated_at', 'desc')->get();
        $dumps = $query->where('is_pinned', false)->orderBy('updated_at', 'desc')->paginate(30)->appends($request->query());

        return view('brain-dump.index', compact('dumps', 'pinnedDumps', 'search'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'content' => 'required|string|max:10000',
            'is_pinned' => 'boolean',
        ]);
        auth()->user()->brainDumps()->create($data);
        return redirect()->route('brain-dumps.index')->with('success', 'Brain dump created successfully.');
    }

    public function update(Request $request, BrainDump $brainDump)
    {
        $this->authorize('update', $brainDump);
        $data = $request->validate([
            'content' => 'required|string|max:10000',
            'is_pinned' => 'boolean',
        ]);
        $brainDump->update($data);
        return redirect()->route('brain-dumps.index')->with('success', 'Brain dump updated successfully.');
    }

    public function destroy(BrainDump $brainDump)
    {
        $this->authorize('delete', $brainDump);
        $brainDump->delete();
        return redirect()->route('brain-dumps.index')->with('success', 'Brain dump deleted successfully.');
    }

    public function toggleArchive(BrainDump $brainDump)
    {
        $this->authorize('update', $brainDump);
        $brainDump->update(['is_archived' => !$brainDump->is_archived]);
        return redirect()->route('brain-dumps.index')->with('success', $brainDump->is_archived ? 'Brain dump archived.' : 'Brain dump unarchived.');
    }

    public function togglePin(BrainDump $brainDump)
    {
        $this->authorize('update', $brainDump);
        $brainDump->update(['is_pinned' => !$brainDump->is_pinned]);
        return redirect()->route('brain-dumps.index');
    }
}
