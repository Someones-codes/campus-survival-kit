<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')
                    ->where(fn ($query) => $query->where('user_id', auth()->id())->orWhereNull('user_id')),
            ],
            'type' => ['required', 'in:income,expense'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'You already have a category with this name.',
        ];
    }
}