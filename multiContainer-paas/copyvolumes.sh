#!/bin/sh
sudo cp ./services/nginx/html/web /var/lib/docker/volumes/web-html/_data/
sudo cp ./services/nginx/html/api /var/lib/docker/volumes/api-html/_data/
sudo cp ./services/flask/dashboard /var/lib/docker/volumes/paas-dashboard/_data/
