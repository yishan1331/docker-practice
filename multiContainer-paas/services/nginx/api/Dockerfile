FROM nginx
LABEL maintainer="yishanjob13@gmail.com"

ARG TZ
RUN apt-get -y update \
    && apt-get install -y vim-gtk less logrotate

CMD nginx -g 'daemon off;'
#https://ithelp.ithome.com.tw/articles/10218044