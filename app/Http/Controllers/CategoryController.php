<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use App\Models\Transaction;

class CategoryController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $categories = Category::where('user_id', $userId)
            ->orWhereNull('user_id')
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $totals = Transaction::where('user_id', $userId)
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        return view('categories.index', [
            'categories' => $categories,
            'totals' => $totals,
        ]);
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create([
            'user_id' => auth()->id(),
            'name' => $request->string('name'),
            'type' => $request->string('type'),
            'is_default' => false,
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Spending unit created.');
    }

    public function destroy(Category $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);

        if ($category->transactions()->exists()) {
            return redirect()
                ->route('categories.index')
                ->with('error', 'This category has transactions logged against it and cannot be deleted.');
        }

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Spending unit deleted.');
    }
}