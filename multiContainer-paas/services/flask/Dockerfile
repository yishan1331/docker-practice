FROM python:2.7
LABEL maintainer="yishanjob13@gmail.com"

ARG TZ

# layer caching for faster builds
COPY requirements.txt /
RUN pip install -r /requirements.txt

RUN apt-get -y update \
    && apt-get install -y vim-gtk less curl cron

ADD ./www/ /var/www/spdpaas/
WORKDIR /var/www/spdpaas

CMD ["uwsgi","-i","spdpaas_uwsgi.ini"]