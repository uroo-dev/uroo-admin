<?php

declare(strict_types=1);

namespace Modules\Subscription\Services;

use Illuminate\Support\Collection;
use Modules\Subscription\Models\Subscription;

class SubscriptionService
{
    public function getStats(int $userId): array
    {
        $subscriptions = Subscription::where('user_id', $userId);
        $activeSubs = (clone $subscriptions)->active();

        $totalMonthly = (clone $activeSubs)->get()->sum(function ($sub) {
            return $sub->billing_cycle === 'monthly' ? $sub->monthly_cost : 0;
        });

        $totalAnnual = (clone $activeSubs)->get()->sum(function ($sub) {
            return $sub->billing_cycle === 'yearly' ? ($sub->annual_cost ?? $sub->monthly_cost * 12) : $sub->monthly_cost * 12;
        });

        return [
            'total_monthly' => (float) $totalMonthly,
            'total_annual' => (float) $totalAnnual,
            'total_all' => (float) $totalMonthly + (float) ($totalAnnual / 12),
            'active_subs' => (clone $activeSubs)->count(),
            'total_subs' => (clone $subscriptions)->count(),
            'paid' => (clone $subscriptions)->where('payment_status', 'paid')->count(),
            'unpaid' => (clone $subscriptions)->where('payment_status', 'unpaid')->count(),
        ];
    }

    public function getUpcomingPayments(int $userId, int $days = 7): Collection
    {
        $date = now()->addDays($days);

        return Subscription::where('user_id', $userId)
            ->active()
            ->where('payment_status', 'unpaid')
            ->where('due_date', '<=', $date)
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
        $subscriptions = Subscription::where('user_id', $userId)->active()->get();

        $monthly = $subscriptions->where('billing_cycle', 'monthly')->sum('monthly_cost');
        $yearlyMonthly = $subscriptions->where('billing_cycle', 'yearly')->sum(function ($sub) {
            return ($sub->annual_cost ?? $sub->monthly_cost * 12) / 12;
        });

        return [
            'monthly_subscriptions' => (float) $monthly,
            'yearly_subscriptions_monthly' => (float) $yearlyMonthly,
            'total_monthly' => (float) $monthly + (float) $yearlyMonthly,
        ];
    }
}
