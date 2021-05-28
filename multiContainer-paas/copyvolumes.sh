#!/bin/sh
sudo cp -R ./services/nginx/html/web/* /var/lib/docker/volumes/web-html/_data/
# sudo chown -R www-data:www-data /var/lib/docker/volumes/web-html/_data/*
sudo cp -R ./services/nginx/html/api/* /var/lib/docker/volumes/api-html/_data/
# sudo chown -R www-data:www-data /var/lib/docker/volumes/api-html/_data/*
sudo cp -R ./services/flask/dashboard/* /var/lib/docker/volumes/paas-dashboard/_data/
# sudo chown -R www-data:www-data /var/lib/docker/volumespaas-dashboard/_data/*

# sudo docker cp $(sudo docker ps | grep flask_app | head -1 | awk '{ print $1 }'):/root/.ssh/id_rsa.pub ~/.ssh/authorized_keys
# sudo chmod 644 ~/.ssh/authorized_keys


docker-compose restart
docker exec -it php /etc/init.d/rsyslog start
docker exec -it php /etc/init.d/cron start
#將crontab作業啟動
docker exec -it php crontab /var/spool/cron/crontabs/root

