<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = in_array($request->query('period'), ['day', 'week', 'month'])
            ? $request->query('period')
            : 'week';

        [$currentStart, $currentEnd] = $this->periodRange($period, now());
        [$previousStart, $previousEnd] = $this->periodRange($period, $this->previousPeriodAnchor($period));

        $userId = auth()->id();

        $current = $this->totalsFor($userId, $currentStart, $currentEnd);
        $previous = $this->totalsFor($userId, $previousStart, $previousEnd);

        $spendingChange = $this->percentChange($previous['expense'], $current['expense']);

        $recentTransactions = Transaction::where('user_id', $userId)
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $categoryBreakdown = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$currentStart, $currentEnd])
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(fn ($group) => $group->sum('amount'))
            ->sortDesc();

        return view('dashboard', [
            'period' => $period,
            'currentStart' => $currentStart,
            'currentEnd' => $currentEnd,
            'income' => $current['income'],
            'expense' => $current['expense'],
            'remaining' => $current['income'] - $current['expense'],
            'previousExpense' => $previous['expense'],
            'spendingChange' => $spendingChange,
            'recentTransactions' => $recentTransactions,
            'categoryBreakdown' => $categoryBreakdown,
        ]);
    }

    private function periodRange(string $period, Carbon $anchor): array
    {
        return match ($period) {
            'day' => [$anchor->copy()->startOfDay(), $anchor->copy()->endOfDay()],
            'month' => [$anchor->copy()->startOfMonth(), $anchor->copy()->endOfMonth()],
            default => [$anchor->copy()->startOfWeek(), $anchor->copy()->endOfWeek()],
        };
    }

    private function previousPeriodAnchor(string $period): Carbon
    {
        return match ($period) {
            'day' => now()->subDay(),
            'month' => now()->subMonthNoOverflow(),
            default => now()->subWeek(),
        };
    }

    private function totalsFor(int $userId, Carbon $start, Carbon $end): array
    {
        $rows = Transaction::where('user_id', $userId)
            ->whereBetween('transaction_date', [$start, $end])
            ->selectRaw("type, SUM(amount) as total")
            ->groupBy('type')
            ->pluck('total', 'type');

        return [
            'income' => (float) ($rows['income'] ?? 0),
            'expense' => (float) ($rows['expense'] ?? 0),
        ];
    }

    private function percentChange(float $previous, float $current): ?float
    {
        if ($previous <= 0) {
            return null;
        }

        return (($current - $previous) / $previous) * 100;
    }
}