FROM nginx
LABEL maintainer="yishanjob13@gmail.com"

ARG TZ
ARG WEB_HTML

RUN apt-get -y update \
    && apt-get install -y vim-gtk less curl

COPY ${WEB_HTML} /var/www/html

# COPY var /var
# RUN chown root:crontab /var/spool/cron/crontabs/root

# CMD service cron start && nginx -g 'daemon off;'
#https://ithelp.ithome.com.tw/articles/10218044

CMD nginx -g 'daemon off;'