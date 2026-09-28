<?php

return [
    'document' => [
        'signatory_name' => env(
            'PNAE_SIGNATORY_NAME',
            'Le Directeur de l’administration électronique'
        ),

        'signatory_title' => env(
            'PNAE_SIGNATORY_TITLE',
            'Autorité administrative compétente'
        ),

        'institution' => env(
            'PNAE_SIGNATORY_INSTITUTION',
            'Plateforme Nationale d’Administration Électronique'
        ),

        'location' => env(
            'PNAE_DOCUMENT_LOCATION',
            'Bangui'
        ),
    ],
];