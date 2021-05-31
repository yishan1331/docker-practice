#!/bin/sh
app=$1
docker exec -it $app /etc/init.d/rsyslog start
docker exec -it $app /etc/init.d/cron start