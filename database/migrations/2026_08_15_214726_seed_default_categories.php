<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        $now = Carbon::now();

        $incomeCategories = [
            'Allowance',
            'Bursary',
            'Part-time Job',
            'Freelance Work',
            'Other Income',
        ];

        $expenseCategories = [
            'Café & Fuel',
            'Textbooks & Tech',
            'Night Out',
            'Rent & Survival',
            'Transport',
            'Data & Airtime',
            'Entertainment',
            'Other',
        ];

        foreach ($incomeCategories as $name) {
            $exists = DB::table('categories')
                ->whereNull('user_id')
                ->where('name', $name)
                ->exists();

            if (! $exists) {
                DB::table('categories')->insert([
                    'user_id' => null,
                    'name' => $name,
                    'type' => 'income',
                    'is_default' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        foreach ($expenseCategories as $name) {
            $exists = DB::table('categories')
                ->whereNull('user_id')
                ->where('name', $name)
                ->exists();

            if (! $exists) {
                DB::table('categories')->insert([
                    'user_id' => null,
                    'name' => $name,
                    'type' => 'expense',
                    'is_default' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('categories')->whereNull('user_id')->where('is_default', true)->delete();
    }
};