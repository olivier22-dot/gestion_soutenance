#!/usr/bin/env bash
# Installe le backend Laravel + PostgreSQL à partir de backend-files/
# Usage : DB_PASSWORD=motdepasse ./install.sh   (variables optionnelles : DB_HOST, DB_PORT, DB_NAME, DB_USER)
set -euo pipefail

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-5432}"
DB_NAME="${DB_NAME:-soutenance}"
DB_USER="${DB_USER:-postgres}"
DB_PASSWORD="${DB_PASSWORD:-}"

[ -d backend ] && { echo "Le dossier 'backend' existe déjà : supprimez-le ou installez à la main (voir README)."; exit 1; }

composer create-project laravel/laravel backend
cd backend

set_env() { # clé valeur : remplace ou ajoute dans .env
  if grep -qE "^#?\s*$1=" .env; then
    sed -i.bak -E "s|^#?\s*$1=.*|$1=$2|" .env
  else
    echo "$1=$2" >> .env
  fi
}
set_env APP_TIMEZONE "Indian/Antananarivo"
set_env DB_CONNECTION pgsql
set_env DB_HOST "$DB_HOST"
set_env DB_PORT "$DB_PORT"
set_env DB_DATABASE "$DB_NAME"
set_env DB_USERNAME "$DB_USER"
set_env DB_PASSWORD "$DB_PASSWORD"
rm -f .env.bak

PGPASSWORD="$DB_PASSWORD" createdb -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" "$DB_NAME" 2>/dev/null || true

php artisan install:api --no-interaction
cp -r ../backend-files/. .
php artisan migrate --seed --force

echo
echo "Backend prêt. Lancez :  cd backend && php artisan serve"
echo "Puis le frontend    :  cd frontend && npm install && npm run dev"
