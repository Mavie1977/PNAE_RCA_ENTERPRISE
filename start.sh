#!/usr/bin/env bash

set -e

echo "Préparation de PNAE-RCA..."

php artisan optimize:clear

php artisan migrate --force

php artisan storage:link || true

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Démarrage du serveur..."

php artisan serve \
    --host=0.0.0.0 \
    --port="${PORT:-8080}"