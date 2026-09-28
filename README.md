# PNAE-RCA V10 Enterprise Stable

Plateforme Nationale d’Administration Électronique - République Centrafricaine.

## Installation locale

```bash
cd PNAE_RCA_V10_Enterprise_Stable
composer install
npm install
copy .env.example .env   # Windows
# ou cp .env.example .env # Linux/Mac
php artisan key:generate
php artisan migrate:fresh --seed
npm run dev
php artisan serve
```

Ouvrir : http://127.0.0.1:8000

## Comptes de test

| Rôle | Email | Mot de passe |
|---|---|---|
| Admin | admin@pnae-rca.cf | password |
| Responsable | responsable@pnae-rca.cf | password |
| Agent | agent@pnae-rca.cf | password |
| Citoyen | citoyen@pnae-rca.cf | password |

## Modules inclus

- Portail public institutionnel
- Authentification simple
- Espace citoyen
- Dépôt et suivi de demandes
- Espace agent
- Espace responsable/admin
- Ministères, démarches, documents, paiements, notifications
- Workflow administratif simple
- Journal d’audit
- Interface responsive aux couleurs RCA

## Important

Ce ZIP ne contient pas `vendor` ni `node_modules`. C’est normal : il faut exécuter `composer install` et `npm install`.
