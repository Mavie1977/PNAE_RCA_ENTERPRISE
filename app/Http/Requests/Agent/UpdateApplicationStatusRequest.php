<?php

namespace App\Http\Requests\Agent;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationStatusRequest extends FormRequest
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
                'in:soumise,en_traitement,validee,rejetee,terminee',
            ],

            'comment' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' =>
                'Veuillez sélectionner un nouveau statut.',

            'status.in' =>
                'Le statut sélectionné est invalide.',

            'comment.max' =>
                'Le commentaire ne doit pas dépasser 2 000 caractères.',
        ];
    }
}