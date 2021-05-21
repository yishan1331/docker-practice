FROM nginx
LABEL maintainer="yishanjob13@gmail.com"

ARG TZ

RUN apt-get -y update \
    && apt-get install -y vim-gtk less curl cron rsyslog

COPY var /var
RUN chown root:crontab /var/spool/cron/crontabs/root

CMD service cron start && nginx -g 'daemon off;'
#https://ithelp.ithome.com.tw/articles/10218044