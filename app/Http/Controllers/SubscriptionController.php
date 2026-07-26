<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        return view('subscriptions.index');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'provider' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'monthly_cost' => 'nullable|numeric|min:0',
            'annual_cost' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'billing_cycle' => 'required|in:monthly,yearly,quarterly',
            'due_date' => 'required|date',
            'payment_status' => 'required|in:paid,unpaid',
            'reminder_days' => 'nullable|integer|min:0|max:90',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:5000',
        ]);

        $data['user_id'] = auth()->id();
        auth()->user()->subscriptions()->create($data);

        return redirect()->route('subscriptions.index')->with('success', 'Subscription created successfully.');
    }

    public function update(\Illuminate\Http\Request $request, Subscription $subscription)
    {
        $this->authorize('update', $subscription);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'provider' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'monthly_cost' => 'nullable|numeric|min:0',
            'annual_cost' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'billing_cycle' => 'required|in:monthly,yearly,quarterly',
            'due_date' => 'required|date',
            'payment_status' => 'required|in:paid,unpaid',
            'reminder_days' => 'nullable|integer|min:0|max:90',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:5000',
        ]);

        $subscription->update($data);

        return redirect()->route('subscriptions.index')->with('success', 'Subscription updated successfully.');
    }

    public function destroy(Subscription $subscription)
    {
        $this->authorize('delete', $subscription);
        $subscription->delete();

        return redirect()->route('subscriptions.index')->with('success', 'Subscription deleted successfully.');
    }

    public function togglePayment(Subscription $subscription)
    {
        $this->authorize('togglePayment', $subscription);
        $subscription->update([
            'payment_status' => $subscription->payment_status === 'paid' ? 'unpaid' : 'paid',
        ]);

        return redirect()->route('subscriptions.index')->with('success', 'Payment status updated.');
    }
}
