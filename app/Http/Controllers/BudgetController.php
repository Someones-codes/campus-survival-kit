<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;

class BudgetController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $budgets = Budget::where('user_id', $userId)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (Budget $budget) use ($userId) {
                [$start, $end] = $this->currentPeriodRange($budget->period);

                $spent = Transaction::where('user_id', $userId)
                    ->where('type', 'expense')
                    ->whereBetween('transaction_date', [$start, $end])
                    ->when($budget->category_id, fn ($q) => $q->where('category_id', $budget->category_id))
                    ->sum('amount');

                $percentUsed = $budget->amount > 0
                    ? min(999, ($spent / $budget->amount) * 100)
                    : 0;

                $status = match (true) {
                    $percentUsed >= 100 => 'over',
                    $percentUsed >= 80 => 'warning',
                    default => 'ok',
                };

                $budget->spent = $spent;
                $budget->remaining = $budget->amount - $spent;
                $budget->percent_used = $percentUsed;
                $budget->status = $status;
                $budget->period_start = $start;
                $budget->period_end = $end;

                return $budget;
            });

        $categories = Category::where('user_id', $userId)
            ->where('type', 'expense')
            ->orWhere(fn ($q) => $q->whereNull('user_id')->where('type', 'expense'))
            ->orderBy('name')
            ->get();

        return view('budgets.index', [
            'budgets' => $budgets,
            'categories' => $categories,
        ]);
    }

    public function store(StoreBudgetRequest $request)
    {
        Budget::create([
            'user_id' => auth()->id(),
            'category_id' => $request->input('category_id') ?: null,
            'name' => $request->string('name'),
            'amount' => $request->input('amount'),
            'period' => $request->string('period'),
        ]);

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Ration created.');
    }

    public function edit(Budget $budget)
    {
        abort_if($budget->user_id !== auth()->id(), 403);

        $categories = Category::where('user_id', auth()->id())
            ->where('type', 'expense')
            ->orWhere(fn ($q) => $q->whereNull('user_id')->where('type', 'expense'))
            ->orderBy('name')
            ->get();

        return view('budgets.edit', [
            'budget' => $budget,
            'categories' => $categories,
        ]);
    }

    public function update(UpdateBudgetRequest $request, Budget $budget)
    {
        abort_if($budget->user_id !== auth()->id(), 403);

        $budget->update([
            'category_id' => $request->input('category_id') ?: null,
            'name' => $request->string('name'),
            'amount' => $request->input('amount'),
            'period' => $request->string('period'),
        ]);

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Ration updated.');
    }

    public function destroy(Budget $budget)
    {
        abort_if($budget->user_id !== auth()->id(), 403);

        $budget->delete();

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Ration removed.');
    }

    private function currentPeriodRange(string $period): array
    {
        return match ($period) {
            'monthly' => [now()->startOfMonth(), now()->endOfMonth()],
            default => [now()->startOfWeek(), now()->endOfWeek()],
        };
    }
}