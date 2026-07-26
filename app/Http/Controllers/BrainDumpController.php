<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BrainDump;

class BrainDumpController extends Controller
{
    public function index()
    {
        return view('brain-dump.index');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'content' => 'required|string|max:10000',
            'is_pinned' => 'boolean',
        ]);

        auth()->user()->brainDumps()->create($data);

        return redirect()->route('brain-dumps.index')->with('success', 'Brain dump created successfully.');
    }

    public function update(\Illuminate\Http\Request $request, BrainDump $brainDump)
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
}
