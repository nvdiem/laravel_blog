<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('categories')->ignore($categoryId),
            ],
            'description' => 'nullable|string|max:1000',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) use ($categoryId) {
                    if ($value) {
                        // Prevent circular reference
                        if ($categoryId && $value == $categoryId) {
                            $fail('Category cannot be its own parent.');
                        }

                        // Check if parent has a parent (max 1 level)
                        $parent = \App\Models\Category::find($value);
                        if ($parent && $parent->parent_id) {
                            $fail('Parent category cannot have its own parent (maximum 1 level deep).');
                        }
                    }
                },
            ],
            'status' => 'required|in:active,inactive',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-generate slug from name if not provided
        if (!$this->has('slug') || empty($this->slug)) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->name),
            ]);
        } else {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->slug),
            ]);
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'slug.required' => 'Category slug is required.',
            'slug.unique' => 'This slug is already taken.',
            'slug.regex' => 'Slug can only contain lowercase letters, numbers, and hyphens.',
            'parent_id.exists' => 'Selected parent category does not exist.',
            'status.in' => 'Status must be either active or inactive.',
        ];
    }
}
