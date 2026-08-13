<?php

namespace App\Http\Controllers;

use App\Models\Budget;
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

        $grade = $this->calculateFinancialGrade($userId, $current['income'], $current['expense'], $spendingChange);

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
            'grade' => $grade,
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

    /**
     * Calculate the Financial Health Grade using three weighted factors:
     * 1. Spending vs Income (up to 50 points)
     * 2. Ration Adherence (up to 30 points)
     * 3. Spending Trend (up to 20 points)
     *
     * If a factor cannot be measured (no income logged, no rations set,
     * no previous period to compare), its points are redistributed into
     * Factor 1 so the student is never unfairly penalized for missing data.
     */
    private function calculateFinancialGrade(int $userId, float $income, float $expense, ?float $spendingChange): array
    {
        $maxFactor1 = 50;
        $maxFactor2 = 30;
        $maxFactor3 = 20;

        $factor1Score = null;
        $factor2Score = null;
        $factor3Score = null;

        // Factor 1: Spending vs Income
        if ($income > 0) {
            $ratio = $expense / $income;

            $factor1Score = match (true) {
                $ratio <= 0.50 => $maxFactor1,
                $ratio <= 0.75 => 40,
                $ratio <= 0.90 => 30,
                $ratio <= 1.00 => 15,
                default => 0,
            };
        }

        // Factor 2: Ration Adherence
        $activeBudgets = Budget::where('user_id', $userId)->get();

        if ($activeBudgets->isNotEmpty()) {
            $onTrackCount = $activeBudgets->filter(function (Budget $budget) use ($userId) {
                [$start, $end] = $this->periodRange(
                    $budget->period === 'monthly' ? 'month' : 'week',
                    now()
                );

                $spent = Transaction::where('user_id', $userId)
                    ->where('type', 'expense')
                    ->whereBetween('transaction_date', [$start, $end])
                    ->when($budget->category_id, fn ($q) => $q->where('category_id', $budget->category_id))
                    ->sum('amount');

                return $budget->amount > 0 && $spent <= $budget->amount;
            })->count();

            $factor2Score = ($onTrackCount / $activeBudgets->count()) * $maxFactor2;
        }

        // Factor 3: Spending Trend
        if (! is_null($spendingChange)) {
            $factor3Score = match (true) {
                $spendingChange <= 0 => $maxFactor3,
                $spendingChange <= 15 => 15,
                $spendingChange <= 30 => 10,
                $spendingChange <= 50 => 5,
                default => 0,
            };
        }

        // Redistribute points from any unmeasurable factor into Factor 1
        $bonusPoints = 0;
        if (is_null($factor2Score)) {
            $bonusPoints += $maxFactor2;
        }
        if (is_null($factor3Score)) {
            $bonusPoints += $maxFactor3;
        }

        // If Factor 1 itself can't be measured (no income), we can't grade at all yet
        if (is_null($factor1Score)) {
            return [
                'available' => false,
                'letter' => null,
                'score' => null,
            ];
        }

        $totalScore = min(100, $factor1Score + $bonusPoints + ($factor2Score ?? 0) + ($factor3Score ?? 0));

        $letter = match (true) {
            $totalScore >= 90 => 'A+',
            $totalScore >= 80 => 'A',
            $totalScore >= 70 => 'B',
            $totalScore >= 60 => 'C',
            $totalScore >= 45 => 'D',
            default => 'F',
        };

        return [
            'available' => true,
            'letter' => $letter,
            'score' => round($totalScore),
        ];
    }
}