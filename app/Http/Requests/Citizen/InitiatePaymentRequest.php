<?php

namespace App\Http\Requests\Citizen;

use Illuminate\Foundation\Http\FormRequest;

class InitiatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->role === 'citoyen';
    }

    public function rules(): array
    {
        return [
            'method' => [
                'required',
                'in:orange_money,mobile_money,carte,virement',
            ],

            'payer_name' => [
                'required',
                'string',
                'max:150',
            ],

            'payer_phone' => [
                'nullable',
                'string',
                'max:30',
                'required_if:method,orange_money,mobile_money',
            ],

            'accept_terms' => [
                'accepted',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'method.required' =>
                'Veuillez sélectionner un moyen de paiement.',

            'method.in' =>
                'Le moyen de paiement sélectionné est invalide.',

            'payer_name.required' =>
                'Le nom du payeur est obligatoire.',

            'payer_phone.required_if' =>
                'Le numéro de téléphone est obligatoire pour le paiement mobile.',

            'accept_terms.accepted' =>
                'Vous devez accepter les conditions de paiement.',
        ];
    }
}