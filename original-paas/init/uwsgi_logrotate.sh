#!/bin/sh
CRONTABLIST=`crontab -l`
THISCRONTABSOURCE="/usr/sbin/logrotate -f /etc/logrotate.d/uwsgi"
if ! echo "$CRONTABLIST" | grep -q "$THISCRONTABSOURCE"; then
   crontab -l | { cat; echo "59 23 * * * $THISCRONTABSOURCE"; } | crontab -
fi