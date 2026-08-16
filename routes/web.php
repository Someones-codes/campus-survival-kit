<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/setup-categories/{token}', function (string $token) {
    if ($token !== env('SEED_SECRET')) {
        abort(404);
    }

    $now = now();

    $incomeCategories = [
        'Allowance', 'Bursary', 'Part-time Job', 'Freelance Work', 'Other Income',
    ];

    $expenseCategories = [
        'Café & Fuel', 'Textbooks & Tech', 'Night Out', 'Rent & Survival',
        'Transport', 'Data & Airtime', 'Entertainment', 'Other',
    ];

    $inserted = [];

    foreach ($incomeCategories as $name) {
        $exists = \Illuminate\Support\Facades\DB::table('categories')
            ->whereNull('user_id')->where('name', $name)->exists();

        if (! $exists) {
            \Illuminate\Support\Facades\DB::table('categories')->insert([
                'user_id' => null,
                'name' => $name,
                'type' => 'income',
                'is_default' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $inserted[] = $name;
        }
    }

    foreach ($expenseCategories as $name) {
        $exists = \Illuminate\Support\Facades\DB::table('categories')
            ->whereNull('user_id')->where('name', $name)->exists();

        if (! $exists) {
            \Illuminate\Support\Facades\DB::table('categories')->insert([
                'user_id' => null,
                'name' => $name,
                'type' => 'expense',
                'is_default' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $inserted[] = $name;
        }
    }

    $total = \Illuminate\Support\Facades\DB::table('categories')->whereNull('user_id')->count();

    return 'Inserted: ' . implode(', ', $inserted) . ' | Total default categories now in DB: ' . $total;
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('transactions', TransactionController::class);

    Route::resource('transactions', TransactionController::class);

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::resource('budgets', BudgetController::class)->except(['show', 'create']);

    
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

});

require __DIR__.'/auth.php';