#!/bin/sh
service mysql start
service postgresql start

mysql -uroot -psapido -e "create database paas_dashboard"
mysql -uroot -psapido -e "create database IOT"
mysql -uroot -psapido IOT < /init/dbsql/iot.sql
mysql -uroot -psapido -e "create database APIDOC"
mysql -uroot -psapido APIDOC < /init/dbsql/APIDOC.sql
mysql -uroot -psapido -e "create user 'paas'@'localhost' IDENTIFIED BY 'sapido';"
mysql -uroot -psapido -e "grant ALL PRIVILEGES ON *.* TO 'paas'@'localhost';"
mysql -uroot -psapido -e "FLUSH PRIVILEGES;"
psql -U postgres -c "CREATE ROLE paas;"
psql -U postgres -c "ALTER ROLE paas WITH NOSUPERUSER INHERIT NOCREATEROLE CREATEDB LOGIN NOREPLICATION NOBYPASSRLS PASSWORD 'sapido';"
psql -U postgres -f /init/dbsql/site2.sql