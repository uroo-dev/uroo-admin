<?php

namespace Modules\Subscription\Services;

use Illuminate\Support\Collection;
use Modules\Subscription\Models\Subscription;

class SubscriptionService
{
    public function getStats(int $userId): array
    {
        $base = Subscription::where('user_id', $userId);
        $activeSubs = (clone $base)->active();

        $totalMonthly = (clone $activeSubs)
            ->where('billing_cycle', 'monthly')
            ->sum('monthly_cost');

        $yearlySubs = (clone $activeSubs)
            ->where('billing_cycle', 'yearly')
            ->get(['annual_cost', 'monthly_cost']);

        $totalAnnual = $yearlySubs->sum(function ($sub) {
            return $sub->annual_cost ?? $sub->monthly_cost * 12;
        });

        return [
            'total_monthly' => (float) $totalMonthly,
            'total_annual' => (float) $totalAnnual,
            'total_all' => (float) $totalMonthly + (float) ($totalAnnual / 12),
            'active_subs' => (clone $activeSubs)->count(),
            'total_subs' => (clone $base)->count(),
            'paid' => (clone $base)->where('payment_status', 'paid')->count(),
            'unpaid' => (clone $base)->where('payment_status', 'unpaid')->count(),
        ];
    }

    public function getUpcomingPayments(int $userId, int $days = 7): Collection
    {
        return Subscription::where('user_id', $userId)
            ->active()
            ->where('payment_status', 'unpaid')
            ->where('due_date', '<=', now()->addDays($days))
            ->orderBy('due_date', 'asc')
            ->get();
    }

    public function getCategories(int $userId): Collection
    {
        return Subscription::where('user_id', $userId)
            ->whereNotNull('category')
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();
    }

    public function getMonthlyReport(int $userId): array
    {
        $monthly = Subscription::where('user_id', $userId)
            ->active()
            ->where('billing_cycle', 'monthly')
            ->sum('monthly_cost');

        $yearlyMonthly = Subscription::where('user_id', $userId)
            ->active()
            ->where('billing_cycle', 'yearly')
            ->get(['annual_cost', 'monthly_cost'])
            ->sum(fn ($sub) => ($sub->annual_cost ?? $sub->monthly_cost * 12) / 12);

        return [
            'monthly_subscriptions' => (float) $monthly,
            'yearly_subscriptions_monthly' => (float) $yearlyMonthly,
            'total_monthly' => (float) $monthly + (float) $yearlyMonthly,
        ];
    }
}