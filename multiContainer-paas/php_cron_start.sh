#!/bin/sh
docker exec -it php /etc/init.d/rsyslog start
docker exec -it php /etc/init.d/cron start