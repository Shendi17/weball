#!/bin/bash
# Crée (si besoin) la base PostgreSQL « weball » dans ines-postgres et y importe un export
# mysqldump de l'ancienne base PHP. Pas besoin de MySQL : le fichier .sql est lu directement.
# Les comptes (users, user_services) sont ignorés. Relançable : l'import remplace les fiches.
#
#   bash python/deploy/import.sh /chemin/weball_contenu.sql
set -euo pipefail
DUMP="$(realpath "${1:?Usage : import.sh export.sql}")"
cd "$(dirname "$0")"
set -a; source .env; set +a
NET="${INES_NETWORK:-ines_ines-network}"

# Base et utilisateur « weball » (mot de passe repris de WEBALL_DATABASE_URL).
MDP="$(sed -E 's#.*://weball:([^@]+)@.*#\1#' <<<"$WEBALL_DATABASE_URL")"
docker exec -i ines-postgres psql -v ON_ERROR_STOP=1 -U "${POSTGRES_USER:-ines}" -d postgres <<SQL
SELECT 'CREATE ROLE weball LOGIN PASSWORD ''$MDP''' WHERE NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'weball')\gexec
SELECT 'CREATE DATABASE weball OWNER weball' WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'weball')\gexec
SQL

echo "▶ Import de $(basename "$DUMP")…"
docker run --rm --network "$NET" -v "$(realpath ../..)":/src -v "$DUMP":/dump.sql:ro -w /src/python \
  -e WEBALL_DATABASE_URL python:3.12-slim \
  sh -c "pip install -q -r requirements.txt && python -m tools.import_mysql_dump /dump.sql"
echo "✅ Import terminé."
