<?php

namespace Database\Seeders;

use App\Models\Ministry;
use App\Models\Procedure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogueSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'Ministère de l’Intérieur',
                [
                    'Carte nationale d’identité',
                    'Passeport',
                    'Autorisation administrative',
                ],
            ],
            [
                'Ministère de la Fonction Publique',
                [
                    'Intégration dans la fonction publique',
                    'Demande d’avancement',
                    'Congé administratif',
                ],
            ],
            [
                'Ministère des Finances',
                [
                    'Numéro d’identification fiscale',
                    'Quitus fiscal',
                    'Paiement taxe',
                ],
            ],
            [
                'Ministère de l’Éducation',
                [
                    'Demande d’équivalence',
                    'Inscription concours',
                    'Attestation scolaire',
                ],
            ],
            [
                'Ministère de la Santé',
                [
                    'Certificat médical',
                    'Autorisation sanitaire',
                    'Carte sanitaire',
                ],
            ],
            [
                'Ministère du Commerce',
                [
                    'Registre de commerce',
                    'Licence commerciale',
                    'Autorisation d’activité',
                ],
            ],
        ];

        foreach ($items as [$name, $procedures]) {

            $ministry = Ministry::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' =>
                        'Ministère connecté à la plateforme nationale PNAE-RCA.',
                    'active' => true,
                ]
            );

            foreach ($procedures as $index => $title) {

                Procedure::firstOrCreate(
                    ['slug' => Str::slug($title)],
                    [
                        'ministry_id' => $ministry->id,
                        'title' => $title,
                        'description' =>
                            'Démarche administrative disponible en ligne via le guichet numérique.',
                        'required_documents' => [
                            'Pièce d’identité',
                            'Justificatif de domicile',
                            'Formulaire signé',
                        ],
                        'fee' => ($index + 1) * 1000,
                        'processing_days' => 7 + $index,
                        'active' => true,
                    ]
                );
            }
        }
    }
}