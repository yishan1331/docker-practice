#!/bin/sh
echo "@@@@@@@@@@@@@@@"
/etc/init.d/redis-server start
echo "redis-server start"
/etc/init.d/rsyslog start
echo "service rsyslog start"
/etc/init.d/cron start
echo "service cron start"
crontab /var/spool/cron/crontabs/root

#因為以crontab執行的程式無法抓到外層的環境變數
#把docker設定的環境變數輸出到檔案中，以便使用
scriptPath=$(dirname "$(readlink -f "$0")")
printenv | sed 's/^\(.*\)$/export \1/g' > ${scriptPath}/.env.sh
chmod +x ${scriptPath}/.env.sh

celery -A celeryWorker multi start 20 --pidfile=/run/celery/%n%I.pid -l=${CELERYLOG} --logfile=/var/log/celery/%n%I.log -Q L-queue1 -c 1000 -P gevent -O fair
echo "start"

tail -F /var/log/syslog