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

COPY ./uwsgi /etc/logrotate.d/

WORKDIR /var/app

# ENTRYPOINT ["/wait-for-it.sh","redis:6379","postgres:5432","-t","5","--","celery -A celeryWorker worker --pidfile=/var/run/worker.pid --loglevel=ERROR --logfile=/var/log/celery/worker.log -c 1000 -P gevent -O fair"]
ENTRYPOINT ["./start.sh"]

# CMD tail -f /dev/null
# ENTRYPOINT celery -A celeryWorker worker --loglevel=info -Q L-queue1 -c 1000 -P gevent -O fair
#-Q celery_worker
# CMD ["./wait-for-it.sh", "redis:6379"]
# ENTRYPOINT ["/bin/sh","-c","/wait-for-it.sh redis:6379 -t 5 -- celery -A celeryWorker worker --loglevel=info"]
# ENTRYPOINT ["celery","-A","celeryWorker", "worker", "--loglevel=info"]