FROM python:2.7
LABEL maintainer="yishanjob13@gmail.com"

ARG TZ

# layer caching for faster builds
COPY requirements.txt /
RUN pip install -r /requirements.txt

# RUN apt-get -y update \
#     && apt-get install -y vim-gtk less curl cron

#COPY app.py /app.py
COPY var /var
WORKDIR /var/app

# CMD tail -f /dev/null
# ENTRYPOINT celery -A celeryWorker worker --loglevel=info -Q L-queue1 -c 1000 -P gevent -O fair
#-Q celery_worker
ENTRYPOINT ["celery","-A","celeryWorker", "worker", "--loglevel=info"]