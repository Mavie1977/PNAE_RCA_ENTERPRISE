<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && in_array(
                auth()->user()->role,
                ['agent', 'responsable'],
                true
            );
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'in:attendu,recu,valide,rejete',
            ],

            'note' => [
                'nullable',
                'string',
                'max:2000',
                'required_if:status,rejete',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' =>
                'Veuillez sélectionner un statut documentaire.',

            'status.in' =>
                'Le statut documentaire sélectionné est invalide.',

            'note.required_if' =>
                'Le motif du rejet est obligatoire.',

            'note.max' =>
                'La note ne doit pas dépasser 2 000 caractères.',
        ];
    }
}