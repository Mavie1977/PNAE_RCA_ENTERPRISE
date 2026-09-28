<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMinistryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:190',
                'unique:ministries,name',
            ],

            'code' => [
                'nullable',
                'string',
                'max:30',
                'unique:ministries,code',
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
}