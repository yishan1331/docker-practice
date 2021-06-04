FROM python:2.7
LABEL maintainer="yishanjob13@gmail.com"

ARG TZ

# layer caching for faster builds
COPY requirements.txt /
RUN pip install -r /requirements.txt

RUN apt-get -y update \
    && apt-get install -y vim-gtk less curl cron rsyslog logrotate

#COPY app.py /app.py
COPY var /var

RUN chown root:crontab /var/spool/cron/crontabs/root

COPY wait-for-it.sh /wait-for-it.sh
RUN chmod +x /wait-for-it.sh

COPY ./celery /etc/logrotate.d/

WORKDIR /var/app

ENTRYPOINT ["./start.sh"]
#https://stackoverflow.com/questions/36127551/how-to-keep-a-service-running-on-a-docker-container/37648139