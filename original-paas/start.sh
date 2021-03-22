#!/bin/sh
service nginx start
# systemctl enable nginx.service
service php7.2-fpm start
service mysql start
# systemctl enable mysql.service
service postgresql start
# systemctl enable postgresql.service
/etc/init.d/redis-server start

supervisord -c /etc/supervisor/supervisord.conf
uwsgi -i /var/www/spdpaas/spdpaas_uwsgi.ini