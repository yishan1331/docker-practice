#!/bin/sh
sudo cp -R ./services/nginx/html/web/* /var/lib/docker/volumes/web-html/_data/
sudo chown -R www-data:www-data /var/lib/docker/volumes/web-html/_data/*
sudo cp -R ./services/nginx/html/api/* /var/lib/docker/volumes/api-html/_data/
sudo chown -R www-data:www-data /var/lib/docker/volumes/api-html/_data/*
sudo cp -R ./services/flask/dashboard/* /var/lib/docker/volumes/paas-dashboard/_data/
sudo chown -R www-data:www-data /var/lib/docker/volumes/paas-dashboard/_data/*

docker-compose restart nginx_api
docker-compose restart nginx_web
docker-compose restart flask_app
