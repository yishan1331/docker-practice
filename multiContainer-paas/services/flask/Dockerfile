FROM python:2.7
LABEL maintainer="yishanjob13@gmail.com"

ARG TZ

# layer caching for faster builds
COPY ./init /init
COPY var /var

RUN apt-get -y update \
    && apt-get install -y vim-gtk less curl cron openssl rsyslog logrotate mailutils

# COPY ./config /root/.ssh/
# RUN ssh-keygen -q -t rsa -N '' -f  /root/.ssh/id_rsa

RUN apt-get install -y gcc g++ zlib1g-dev make\
    libssl-dev libffi-dev build-essential python python-dev\
    default-jre default-jdk memcached\
    libmemcached-dev libpq-dev python2.7-dev

# RUN apt-get build-dep python-psycopg2
RUN apt-get install -y unixodbc-dev python-setuptools

RUN pip install setuptools==44.0.0
RUN pip install -r /init/requirements.txt

RUN openssl enc -aes-128-cbc -in /var/www/spdpaas/config/constants.conf -out /var/www/spdpaas/config/encconstants.conf -pass pass:sapidotest2019
RUN mkdir /var/log/uwsgi
RUN chown -R www-data:adm /var/log/uwsgi

WORKDIR /var/www/spdpaas
RUN tar zxvf /var/www/spdpaas/Authlib-0.14.3.tar.gz
RUN chown -R www-data:www-data /var/www/spdpaas/Authlib-0.14.3
WORKDIR /var/www/spdpaas/Authlib-0.14.3
RUN python setup.py install

RUN chown -R www-data:www-data /var/www/spdpaas
WORKDIR /var/www/spdpaas
RUN rm -R Authlib-0.14.3 Authlib-0.14.3.tar.gz

COPY ./uwsgi /etc/logrotate.d/
# RUN chmod +x /init/uwsgi_logrotate.sh
# RUN /bin/bash /init/uwsgi_logrotate.sh

ENTRYPOINT ["/init/start.sh"]