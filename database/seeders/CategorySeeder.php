<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
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
            Category::create([
                'user_id' => null,
                'name' => $name,
                'type' => 'income',
                'is_default' => true,
            ]);
        }

        foreach ($expenseCategories as $name) {
            Category::create([
                'user_id' => null,
                'name' => $name,
                'type' => 'expense',
                'is_default' => true,
            ]);
        }
    }
}