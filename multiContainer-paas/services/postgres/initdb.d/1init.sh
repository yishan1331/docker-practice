#!/bin/bash
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
    CREATE ROLE $POSTGRES_PAAS_USER;
    ALTER ROLE $POSTGRES_PAAS_USER WITH NOSUPERUSER INHERIT NOCREATEROLE CREATEDB LOGIN NOREPLICATION NOBYPASSRLS PASSWORD '$POSTGRES_PAAS_PASSWORD';
EOSQL

# pg_restore -U postgres --no-owner --role=paas -c -d site2 /docker-entrypoint-initdb.d/chunzu_site2.dump