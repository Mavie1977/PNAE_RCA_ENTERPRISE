@props([
    'status',
    'label' => null,
])

@php
    $normalizedStatus = strtolower((string) $status);

    $labels = [
        'soumise' => 'Soumise',
        'en_attente' => 'En attente',
        'en_traitement' => 'En traitement',
        'validee' => 'Validée',
        'rejetee' => 'Rejetée',
        'terminee' => 'Terminée',
        'actif' => 'Actif',
        'active' => 'Active',
        'inactif' => 'Inactif',
        'inactive' => 'Inactive',
        'paye' => 'Payé',
        'payee' => 'Payée',
        'non_paye' => 'Non payé',
        'annulee' => 'Annulée',
		
		'attendu' => 'Attendu',
        'recu' => 'Reçu',
        'valide' => 'Validé',
        'rejete' => 'Rejeté',
		
		'initie' => 'Initié',
        'paye' => 'Payé',
        'echoue' => 'Échoué',
        'annule' => 'Annulé',
        'rembourse' => 'Remboursé',
		
		'actif' => 'Valide',
        'revoque' => 'Révoqué',
    ];

    $displayLabel = $label
        ?? ($labels[$normalizedStatus]
        ?? ucfirst(str_replace('_', ' ', $normalizedStatus)));
@endphp

<span
    {{ $attributes->class([
        'component-status-badge',
        'status-' . $normalizedStatus,
    ]) }}
>
    {{ $displayLabel }}
</span>