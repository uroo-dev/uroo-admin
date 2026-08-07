<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\SavingsGoal;
use App\Models\SavingsTransaction;
use App\Services\SavingsService;
use Illuminate\Http\Request;

class SavingsController extends Controller
{
    public function index(Request $request)
    {
        $service = app(SavingsService::class);
        $stats = $service->getStats(auth()->id());
        $goals = SavingsGoal::where('user_id', auth()->id())
            ->withCount('transactions')
            ->orderBy('is_completed', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return view('savings.index', compact('goals', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'nullable|numeric|min:0',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'deadline' => 'nullable|date',
            'notes' => 'nullable|string|max:5000',
        ]);
        $data['user_id'] = auth()->id();
        $data['current_amount'] ??= 0;
        SavingsGoal::create($data);
        return redirect()->route('savings.index')->with('success', 'Savings goal created successfully.');
    }

    public function update(Request $request, SavingsGoal $goal)
    {
        $this->authorize('update', $goal);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'nullable|numeric|min:0',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'deadline' => 'nullable|date',
            'notes' => 'nullable|string|max:5000',
        ]);
        $goal->update($data);
        return redirect()->route('savings.index')->with('success', 'Savings goal updated successfully.');
    }

    public function destroy(SavingsGoal $goal)
    {
        $this->authorize('delete', $goal);
        $goal->transactions()->delete();
        $goal->delete();
        return redirect()->route('savings.index')->with('success', 'Savings goal deleted successfully.');
    }

    public function deposit(Request $request, SavingsGoal $goal)
    {
        $this->authorize('deposit', $goal);
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
        ]);
        SavingsTransaction::create(['goal_id' => $goal->id, 'type' => 'deposit', 'amount' => $data['amount'], 'description' => $data['description'] ?? 'Deposit']);
        $goal->increment('current_amount', $data['amount']);
        if ($goal->current_amount >= $goal->target_amount) {
            $goal->update(['is_completed' => true]);
        }
        return redirect()->route('savings.index')->with('success', 'Deposit added successfully.');
    }

    public function withdraw(Request $request, SavingsGoal $goal)
    {
        $this->authorize('withdraw', $goal);
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
        ]);
        SavingsTransaction::create(['goal_id' => $goal->id, 'type' => 'withdraw', 'amount' => $data['amount'], 'description' => $data['description'] ?? 'Withdrawal']);
        $goal->decrement('current_amount', $data['amount']);
        $goal->update(['is_completed' => false]);
        return redirect()->route('savings.index')->with('success', 'Withdrawal added successfully.');
    }
}
