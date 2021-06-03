#!/bin/sh
/etc/init.d/rsyslog start
crontab /var/spool/cron/crontabs/root
/etc/init.d/cron start

# tail -F /var/log/syslog
/wait-for-it.sh redis:6379 postgres:5432 -t 5 -- /usr/local/bin/celery -A celeryWorker worker --pidfile=/var/run/worker.pid --loglevel=DEBUG --logfile=/var/log/celery/worker.log -c 1000 -P gevent -O fair