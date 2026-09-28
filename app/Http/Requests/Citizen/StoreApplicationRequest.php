<?php

namespace App\Http\Requests\Citizen;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->role === 'citoyen';
    }

    public function rules(): array
    {
        return [
            'procedure_id' => [
                'required',
                'integer',
                'exists:procedures,id',
            ],

            'priority' => [
                'nullable',
                'in:normale,urgente',
            ],

            'message' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'documents' => [
                'nullable',
                'array',
                'max:5',
            ],

            'documents.*' => [
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'procedure_id.required' =>
                'Veuillez sélectionner une démarche.',

            'procedure_id.exists' =>
                'La démarche sélectionnée est invalide.',

            'priority.in' =>
                'La priorité sélectionnée est invalide.',

            'message.max' =>
                'Le message ne doit pas dépasser 5 000 caractères.',

            'documents.max' =>
                'Vous pouvez déposer au maximum 5 fichiers.',

            'documents.*.mimes' =>
                'Les documents doivent être au format PDF, JPG, JPEG ou PNG.',

            'documents.*.max' =>
                'Chaque document ne doit pas dépasser 5 Mo.',
        ];
    }
}