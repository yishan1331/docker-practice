#!/bin/sh
mysql -uroot -psapido -e "create database paas_dashboard"
mysql -uroot -psapido -e "create database IOT"
mysql -uroot -psapido IOT < ./iot.sql
mysql -uroot -psapido -e "create database APIDOC"
mysql -uroot -psapido APIDOC < ./APIDOC.sql
mysql -uroot -psapido -e "create user 'paas'@'%' IDENTIFIED BY 'sapido@admin';"
mysql -uroot -psapido -e "grant ALL PRIVILEGES ON *.* TO 'paas'@'%';"
mysql -uroot -psapido -e "FLUSH PRIVILEGES;"