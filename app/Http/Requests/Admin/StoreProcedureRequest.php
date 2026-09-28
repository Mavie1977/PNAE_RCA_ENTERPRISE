<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreProcedureRequest extends FormRequest
{
    public function authorize(): bool
{
    return $this->user() !== null
        && $this->user()->active
        && $this->user()->role === 'admin';
}

    public function rules(): array
    {
        return [
            'ministry_id' => [
                'required',
                'integer',
                'exists:ministries,id',
            ],

            'title' => [
                'required',
                'string',
                'max:190',
            ],

            'description' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'fee' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999',
            ],

            'processing_days' => [
                'nullable',
                'integer',
                'min:1',
                'max:3650',
            ],

            'payment_required' => [
                'nullable',
                'boolean',
            ],

            'official_document_required' => [
                'nullable',
                'boolean',
            ],

            'active' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (
                    $this->boolean('payment_required')
                    && (float) $this->input('fee', 0) <= 0
                ) {
                    $validator->errors()->add(
                        'fee',
                        'Une démarche avec paiement obligatoire doit avoir un tarif supérieur à zéro.'
                    );
                }
            },
        ];
    }
}