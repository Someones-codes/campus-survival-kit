<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = in_array($request->query('period'), ['daily', 'weekly', 'monthly'])
            ? $request->query('period')
            : 'weekly';

        $userId = auth()->id();

        [$start, $end] = $this->periodRange($period, now());
        [$prevStart, $prevEnd] = $this->periodRange($period, $this->previousAnchor($period));

        $transactions = Transaction::where('user_id', $userId)
            ->whereBetween('transaction_date', [$start, $end])
            ->with('category')
            ->get();

        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');
        $netBalance = $income - $expense;
        $transactionCount = $transactions->count();

        $previousExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$prevStart, $prevEnd])
            ->sum('amount');

        $categoryBreakdown = $transactions
            ->where('type', 'expense')
            ->groupBy('category.name')
            ->map(fn ($group) => [
                'total' => $group->sum('amount'),
                'count' => $group->count(),
            ])
            ->sortByDesc('total');

        $daysInPeriod = (int) $start->diffInDays($end) + 1;
        $averageDailySpend = $daysInPeriod > 0 ? $expense / $daysInPeriod : 0;

        $highestSpendingDay = null;
        if ($period !== 'daily') {
            $highestSpendingDay = $transactions
                ->where('type', 'expense')
                ->groupBy(fn ($t) => $t->transaction_date->format('Y-m-d'))
                ->map(fn ($group) => $group->sum('amount'))
                ->sortDesc()
                ->take(1);
        }

        return view('reports.index', [
            'period' => $period,
            'start' => $start,
            'end' => $end,
            'income' => $income,
            'expense' => $expense,
            'netBalance' => $netBalance,
            'transactionCount' => $transactionCount,
            'previousExpense' => $previousExpense,
            'spendingChange' => $this->percentChange($previousExpense, $expense),
            'categoryBreakdown' => $categoryBreakdown,
            'averageDailySpend' => $averageDailySpend,
            'highestSpendingDay' => $highestSpendingDay,
        ]);
    }

    private function periodRange(string $period, Carbon $anchor): array
    {
        return match ($period) {
            'daily' => [$anchor->copy()->startOfDay(), $anchor->copy()->endOfDay()],
            'monthly' => [$anchor->copy()->startOfMonth(), $anchor->copy()->endOfMonth()],
            default => [$anchor->copy()->startOfWeek(), $anchor->copy()->endOfWeek()],
        };
    }

    private function previousAnchor(string $period): Carbon
    {
        return match ($period) {
            'daily' => now()->subDay(),
            'monthly' => now()->subMonthNoOverflow(),
            default => now()->subWeek(),
        };
    }

    private function percentChange(float $previous, float $current): ?float
    {
        if ($previous <= 0) {
            return null;
        }

        return (($current - $previous) / $previous) * 100;
    }
}