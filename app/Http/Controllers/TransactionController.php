<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::where('user_id', auth()->id())
            ->with('category')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date('date_to'));
        }

        $sort = $request->string('sort', 'newest');

        match ($sort) {
            'oldest' => $query->reorder('transaction_date', 'asc')->orderBy('created_at', 'asc'),
            'highest' => $query->reorder('amount', 'desc'),
            'lowest' => $query->reorder('amount', 'asc'),
            default => null,
        };

        $transactions = $query->paginate(15)->withQueryString();

        $categories = Category::where('user_id', auth()->id())
            ->orWhereNull('user_id')
            ->orderBy('name')
            ->get();

        return view('transactions.index', [
            'transactions' => $transactions,
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        $categories = Category::where('user_id', auth()->id())
            ->orWhereNull('user_id')
            ->orderBy('name')
            ->get();

        return view('transactions.create', [
            'categories' => $categories,
        ]);
    }

    public function store(StoreTransactionRequest $request)
    {
        Transaction::create([
            'user_id' => auth()->id(),
            'category_id' => $request->integer('category_id'),
            'type' => $request->string('type'),
            'amount' => $request->input('amount'),
            'description' => $request->string('description'),
            'note' => $request->input('note'),
            'transaction_date' => $request->date('transaction_date'),
        ]);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaction logged successfully.');
    }

    public function edit(Transaction $transaction)
    {
        abort_if($transaction->user_id !== auth()->id(), 403);

        $categories = Category::where('user_id', auth()->id())
            ->orWhereNull('user_id')
            ->orderBy('name')
            ->get();

        return view('transactions.edit', [
            'transaction' => $transaction,
            'categories' => $categories,
        ]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        abort_if($transaction->user_id !== auth()->id(), 403);

        $transaction->update([
            'category_id' => $request->integer('category_id'),
            'type' => $request->string('type'),
            'amount' => $request->input('amount'),
            'description' => $request->string('description'),
            'note' => $request->input('note'),
            'transaction_date' => $request->date('transaction_date'),
        ]);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        abort_if($transaction->user_id !== auth()->id(), 403);

        $transaction->delete();

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaction deleted.');
    }
}