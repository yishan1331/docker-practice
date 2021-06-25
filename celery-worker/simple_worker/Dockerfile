FROM python:2.7
LABEL maintainer="yishanjob13@gmail.com"

# #設定中文語系 https://ubuntuqa.com/zh-tw/article/9876.html
RUN apt-get update && apt-get install -y locales && rm -rf /var/lib/apt/lists/* \
    && locale-gen "C.UTF-8"
ENV LANG=C.UTF-8
    # LANGUAGE=C:en \
    # LC_ALL=C.UTF-8

#設定時區 https://serverfault.com/questions/683605/docker-container-time-timezone-will-not-reflect-changes/683651
ENV TZ=Asia/Taipei
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

#加上設定環境變數
ARG SYSTEMLIST
ARG CELERYLOG

RUN apt-get -y update \
    && apt-get install -y vim-gtk less curl cron rsyslog

RUN apt-get -y install redis-server
COPY redis.conf /etc/redis/redis.conf

# RUN groupadd user && useradd --create-home --home-dir /home/user -g user user
# WORKDIR /home/user
COPY var /var
COPY run /run
COPY requirements.txt /
RUN pip install -r /requirements.txt

# RUN pip install redis
# # RUN pip install kombu==4.6.3
# # RUN pip install gevent

# RUN pip install celery==4.3.0
# RUN pip install celery[redis]

# RUN { \
# 	echo 'import os'; \
# 	echo "BROKER_URL = os.environ.get('CELERY_BROKER_URL', 'amqp://')"; \
# } > celeryconfig.py

# # --link some-rabbit:rabbit "just works"
# ENV CELERY_BROKER_URL amqp://guest@rabbit
RUN chown root:crontab /var/spool/cron/crontabs/root
RUN chmod +x /var/app/start.sh
RUN chmod +x /var/app/use_env_run_python.sh

WORKDIR /var/app

EXPOSE 6379

CMD [ "./start.sh" ]

# USER user
# CMD celery -A celeryWorker worker -Q L-queue1 --pidfile=/run/celery.pid -l DEBUG --logfile=/var/log/celery/helper.log
# CMD celery -A celeryWorker multi start 10 --pidfile=/run/celery_%n.pid -l DEBUG --logfile=/var/log/celery/%n%I.log -Q L-queue1 -c 1000 -P gevent -O fair
# CMD ["/bin/bash", "start.sh"]