FROM python:2.7
LABEL maintainer="yishanjob13@gmail.com"

ARG TZ

# layer caching for faster builds
COPY requirements.txt /
COPY ./log/ /var/log/

RUN apt-get -y update \
    && apt-get install -y vim-gtk less curl cron 

RUN apt-get install -y gcc g++ zlib1g-dev make\
    libssl-dev libffi-dev build-essential python python-dev\
    default-jre default-jdk memcached\
    libmemcached-dev libpq-dev python2.7-dev

# RUN apt-get build-dep python-psycopg2
RUN apt-get install -y unixodbc-dev python-setuptools

RUN pip install setuptools==44.0.0
RUN pip install -r /requirements.txt

ADD ./www/ /var/www/spdpaas/
WORKDIR /var/www/spdpaas

CMD ["uwsgi","-i","/var/www/spdpaas/spdpaas_uwsgi.ini"]