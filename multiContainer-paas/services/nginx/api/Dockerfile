FROM nginx
LABEL maintainer="yishanjob13@gmail.com"

ARG TZ

RUN apt-get -y update \
    && apt-get install -y vim-gtk less logrotate

# CMD /bin/sh -c "envsubst '$$UWSGI_PORT' < /etc/nginx/templates/spdpaas_nginx.conf.template > /etc/nginx/conf.d/spdpaas_nginx.conf && exec nginx -g 'daemon off;'"
# CMD /bin/bash -c "envsubst < /etc/nginx/templates/spdpaas_nginx.conf.template > /etc/nginx/conf.d/spdpaas_nginx.conf && nginx -g 'daemon off;'"
#https://ithelp.ithome.com.tw/articles/10218044