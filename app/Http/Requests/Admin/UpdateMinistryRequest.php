<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMinistryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $ministry = $this->route('ministry');

        return [
            'name' => [
                'required',
                'string',
                'max:190',
                Rule::unique('ministries', 'name')
                    ->ignore($ministry->id),
            ],

            'code' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('ministries', 'code')
                    ->ignore($ministry->id),
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'email' => [
                'nullable',
                'email',
                'max:190',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'active' => [
                'nullable',
                'boolean',
            ],
        ];
    }
	
	public function update(
    UpdateMinistryRequest $request,
    Ministry $ministry
): RedirectResponse {
    $data = $request->validated();

    $data['name'] = trim($data['name']);

    $baseSlug = Str::slug($data['name']);
    $slug = $baseSlug;
    $counter = 2;

    while (
        Ministry::where('slug', $slug)
            ->where('id', '!=', $ministry->id)
            ->exists()
    ) {
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }

    $data['slug'] = $slug;
    $data['active'] = $request->boolean('active');

    $ministry->update($data);

    return redirect()
        ->route('admin.ministries.show', $ministry)
        ->with(
            'success',
            'Le ministère a été mis à jour avec succès.'
        );
}
}