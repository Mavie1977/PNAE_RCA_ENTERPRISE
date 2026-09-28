<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Objectifs mensuels nationaux
    |--------------------------------------------------------------------------
    */

    'monthly_targets' => [

        'applications' => (int) env(
            'DECISION_TARGET_APPLICATIONS',
            100
        ),

        'revenue' => (float) env(
            'DECISION_TARGET_REVENUE',
            500000
        ),

        'official_documents' => (int) env(
            'DECISION_TARGET_DOCUMENTS',
            50
        ),

        'processing_days' => (int) env(
            'DECISION_TARGET_PROCESSING_DAYS',
            5
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Seuils d’alerte
    |--------------------------------------------------------------------------
    */

    'alerts' => [

        'submitted_days' => (int) env(
            'DECISION_ALERT_SUBMITTED_DAYS',
            3
        ),

        'processing_days' => (int) env(
            'DECISION_ALERT_PROCESSING_DAYS',
            7
        ),
    ],
];