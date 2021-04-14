#!/bin/sh
celery -A celeryWorker multi start 10 --pidfile=/run/celery_%n.pid -l DEBUG --logfile=/var/log/celery/%n%I.log -Q L-queue1 -c 1000 -P gevent -O fair
echo "start"