FROM nginx
LABEL maintainer="yishanjob13@gmail.com"

ARG TZ
ARG PAAS-DASHBOARD

COPY ${PAAS-DASHBOARD} /var/www/spdpaas/src/dashboard

CMD nginx -g 'daemon off;'
#https://ithelp.ithome.com.tw/articles/10218044