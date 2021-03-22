# FROM ubuntu:16.04
# LABEL maintainer="yishanjob13@gmail.com"

# #設定中文語系 https://ubuntuqa.com/zh-tw/article/9876.html
# RUN apt-get update && apt-get install -y locales && rm -rf /var/lib/apt/lists/* \
#     && locale-gen "en_US.UTF-8"
# ENV LANG=en_US.UTF-8 \
#     LANGUAGE=en_US:en \
#     LC_ALL=en_US.UTF-8

# #設定時區 https://serverfault.com/questions/683605/docker-container-time-timezone-will-not-reflect-changes/683651
# ENV TZ=Asia/Taipei
# RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

FROM python:3.5-slim
LABEL maintainer="yishanjob13@gmail.com"

# RUN groupadd user && useradd --create-home --home-dir /home/user -g user user
# WORKDIR /home/user

COPY ./app /var/app
COPY ./log /var/log/
COPY ./run /run

RUN pip install redis
# RUN pip install gevent

ENV CELERY_VERSION 4.3.0

RUN pip install celery=="$CELERY_VERSION"

# RUN { \
# 	echo 'import os'; \
# 	echo "BROKER_URL = os.environ.get('CELERY_BROKER_URL', 'amqp://')"; \
# } > celeryconfig.py

# # --link some-rabbit:rabbit "just works"
# ENV CELERY_BROKER_URL amqp://guest@rabbit

WORKDIR /var/app

# USER user
# CMD celery -A celeryWorker.celery worker -Q=L-queue1 -c=200 --pidfile=/run/celery_%n.pid -l DEBUG --logfile=/var/log/celery/%n%I.log