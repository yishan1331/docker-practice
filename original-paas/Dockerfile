FROM ubuntu:16.04
LABEL maintainer="yishanjob13@gmail.com"

#設定中文語系 https://ubuntuqa.com/zh-tw/article/9876.html
RUN apt-get update && apt-get install -y locales && rm -rf /var/lib/apt/lists/* \
    && locale-gen "en_US.UTF-8"
ENV LANG=en_US.UTF-8 \
    LANGUAGE=en_US:en \
    LC_ALL=en_US.UTF-8

#設定時區 https://serverfault.com/questions/683605/docker-container-time-timezone-will-not-reflect-changes/683651
ENV TZ=Asia/Taipei
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

WORKDIR /
COPY ./init /init
COPY ./start.sh /
COPY ./copy_to_container/www /var/www
COPY ./copy_to_container/log /var/log/
COPY ./copy_to_container/uwsgi /etc/logrotate.d/
RUN echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections
RUN apt-get -y update \
    && apt-get install -y vim-gtk less curl
RUN echo "\033[44;37m --------------------------------安裝Nginx-------------------------------- \033[0m"
RUN apt-get -y update \
    && apt-get install -y nginx
# RUN /etc/init.d/nginx start \
#     systemctl enable nginx.service
COPY ./copy_to_container/nginx.conf /etc/nginx/
RUN echo "\033[44;37m --------------------------------安裝python-------------------------------- \033[0m"
RUN apt-get -y update \
    && apt-get install -y gcc \
    && apt-get install -y g++ \
    && apt-get install -y zlib1g-dev \
    && apt-get install -y make
RUN apt-get install -y wget && wget https://www.python.org/ftp/python/2.7.12/Python-2.7.12.tgz && tar -zxvf Python-2.7.12.tgz
WORKDIR /Python-2.7.12
RUN ./configure --prefix=/usr/local/python-2.7.12
RUN make && make install
RUN ln -s /usr/local/python-2.7.12/bin/python2.7 /usr/bin/python \
    && ln -s /usr/local/python-2.7.12/bin/python2.7 /usr/bin/python2 \
    && ln -s /usr/local/python-2.7.12/bin/python2.7 /usr/bin/python27 \
    && ln -s /usr/local/python-2.7.12/bin/python2.7 /usr/bin/python2.7
RUN apt-get install -y python-pip
RUN echo "\033[44;37m --------------------------------安裝flask-------------------------------- \033[0m"
WORKDIR /var/www/spdpaas
RUN apt-get install -y python-virtualenv
# RUN virtualenv venv
# ENV VIRTUAL_ENV=/var/www/spdpaas/venv
# ENV PATH="$VIRTUAL_ENV/bin:$PATH"
RUN pip install flask
RUN echo "\033[44;37m --------------------------------安裝uWSGI to redirect API requests to Flask-python-------------------------------- \033[0m"
RUN apt-get install -y libssl-dev \
    && apt-get install -y libffi-dev \
    && apt-get install -y build-essential python python-dev
RUN pip install uwsgi
RUN rm /etc/nginx/sites-enabled/default
RUN ln -s /var/www/spdpaas/spdpaas_nginx.conf /etc/nginx/conf.d/
# RUN mkdir -p /var/log/uwsgi
RUN echo "\033[44;37m --------------------------------uWSGI Emperor-run in background as a service(Running uWSGI via Systemd - Ubuntu 16.04)-------------------------------- \033[0m"
# COPY ./emperor.uwsgi.service /etc/systemd/system/
# RUN chmod 644 /etc/systemd/system/emperor.uwsgi.service
# RUN mkdir -p /etc/uwsgi/vassals
# COPY ./emperor.ini /etc/uwsgi/
# RUN ln -s /var/www/spdpaas/spdpaas_uwsgi.ini /etc/uwsgi/vassals
RUN chown -R www-data:adm /var/log/uwsgi/
# RUN systemctl start emperor.uwsgi
# RUN service emperor.uwsgi start
# RUN systemctl enable emperor.uwsgi
RUN chown -R www-data:adm /var/www/spdpaas
RUN echo "\033[44;37m --------------------------------安裝APIs-------------------------------- \033[0m"
RUN sed -i -- 's/#deb-src/deb-src/g' /etc/apt/sources.list \
    && sed -i -- 's/# deb-src/deb-src/g' /etc/apt/sources.list
RUN apt-get -y update \
    && apt-get install -y default-jre \
    && apt-get install -y default-jdk \
    && apt-get install -y memcached \
    && apt-get install -y libmemcached-dev \
    && apt-get install -y libpq-dev python2.7-dev \
    && apt-get build-dep -y python-psycopg2 \
    && apt-get install -y python-setuptools
RUN pip install setuptools==44.0.0
RUN pip install -r /init/requirements.txt
RUN echo "\033[44;37m --------------------------------安裝自定義third-party library-------------------------------- \033[0m"
RUN tar zxvf /init/Authlib-0.14.3.tar.gz
WORKDIR /var/www/spdpaas/Authlib-0.14.3
RUN python setup.py install
WORKDIR /var/www/spdpaas
RUN rm -R /var/www/spdpaas/Authlib-0.14.3
RUN echo "\033[44;37m --------------------------------安裝mariaDB10.2-------------------------------- \033[0m"
RUN apt-get -y update
RUN apt-get install -y software-properties-common
RUN apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xF1656F24C74CD1D8
RUN add-apt-repository 'deb [arch=amd64,arm64,i386,ppc64el] http://ftp.ubuntu-tw.org/mirror/mariadb/repo/10.2/ubuntu xenial main'
RUN apt-get -y update
RUN echo 'mariadb-server-10.2 mysql-server/root_password password sapido' | debconf-set-selections
RUN echo 'mariadb-server-10.2 mysql-server/root_password_again password sapido' | debconf-set-selections
RUN apt-get -y install mariadb-server
RUN sed -i '20a#Yishan add\ndefault-character-set=utf8mb4' /etc/mysql/my.cnf \
    && sed -i '143a#Yishan add\ncollation-server = utf8mb4_unicode_ci\ninit-connect="SET NAMES utf8mb4"\ninit_connect="SET collation_connection = utf8mb4_unicode_ci"\ncharacter-set-server = utf8mb4\nskip-character-set-client-handshake' /etc/mysql/my.cnf \
    && sed -i '186a#Yishan add\ndefault-character-set=utf8mb4' /etc/mysql/my.cnf
# RUN service mysql start && /etc/init.d/mysql start
RUN echo "\033[44;37m --------------------------------mysql create database、匯入資料與建立遠端使用者-------------------------------- \033[0m"
# COPY ./APIDOC.sql /docker-entrypoint-initdb.d
# COPY ./dbsql/iot.sql /
# COPY ./dbsql/APIDOC.sql /
# RUN mysql -uroot -psapido -e "create database paas_dashboard" \
#     && mysql -uroot -psapido -e "create database APIDOC" \
#     && mysql -uroot -psapido APIDOC < ./APIDOC.sql
# RUN mysql -uroot -psapido -e "create user 'paas'@'localhost' IDENTIFIED BY 'sapido';" \
#     && mysql -uroot -psapido -e "grant ALL PRIVILEGES ON *.* TO 'paas'@'localhost';" \
#     && mysql -uroot -psapido -e "FLUSH PRIVILEGES;"
RUN echo "\033[44;37m --------------------------------安裝postgresql9.5-------------------------------- \033[0m"
RUN touch /etc/apt/sources.list.d/postgresql.list
RUN echo "deb http://apt.postgresql.org/pub/repos/apt/ trusty-pgdg main 9.5" >> /etc/apt/sources.list.d/postgresql.list
RUN apt-get install -y wget && wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add -
RUN apt-get -y update && apt-get install -y postgresql-9.5
RUN sed -i '85s/peer/trust/' /etc/postgresql/9.5/main/pg_hba.conf \
    && sed -i '90s/peer/md5/' /etc/postgresql/9.5/main/pg_hba.conf \
    && sed -i '92s/32/24/' /etc/postgresql/9.5/main/pg_hba.conf \
    && sed -i '92s/md5/trust/' /etc/postgresql/9.5/main/pg_hba.conf \
    && sed -i '59s/localhost/*/' /etc/postgresql/9.5/main/postgresql.conf \
    && sed -i '59s/#listen_address/listen_address/' /etc/postgresql/9.5/main/postgresql.conf \
    && sed -i '63s/5432/5680/' /etc/postgresql/9.5/main/postgresql.conf
# RUN service postgresql start && systemctl enable postgresql.service
# COPY ./dbsql/site2.sql /
# COPY ./postgres.sql /docker-entrypoint-initdb.d
# RUN -u postgres psql -c "CREATE ROLE paas;" \
#     && -u postgres psql -c "ALTER ROLE paas WITH NOSUPERUSER INHERIT NOCREATEROLE CREATEDB LOGIN NOREPLICATION NOBYPASSRLS PASSWORD 'sapido';"
RUN echo "\033[44;37m --------------------------------安裝redis-------------------------------- \033[0m"
RUN apt-get -y install redis-server
RUN sed -i '69s/bind 127.0.0.1/bind 0.0.0.0 ::/' /etc/redis/redis.conf #開放遠端連線
RUN sed -i '79s/timeout 0/timeout 3600/' /etc/redis/redis.conf #修改timeout時間
RUN sed -i '396s/# requirepass foobared/requirepass sapido/' /etc/redis/redis.conf #修改登入密碼
# RUN /etc/init.d/redis-server restart
RUN echo "\033[44;37m --------------------------------安裝Microsoft ODBC driver for SQL Server給api使用(有需要連到mssql的需安裝)-------------------------------- \033[0m"
RUN apt-get install -y apt-transport-https
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
#Ubuntu 16.04
RUN curl https://packages.microsoft.com/config/ubuntu/16.04/prod.list > /etc/apt/sources.list.d/mssql-release.list
RUN apt-get -y update \
    && ACCEPT_EULA=Y apt-get install -y --allow-unauthenticated msodbcsql17 \
    && ACCEPT_EULA=Y apt-get install -y --allow-unauthenticated mssql-tools
RUN echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bash_profile
RUN echo 'export PATH="$PATH:/opt/mssql-tools/bin"' >> ~/.bashrc
RUN /bin/bash -c "source ~/.bashrc"
# optional: for unixODBC development headers
RUN apt-get install -y unixodbc-dev
RUN pip install pyodbc==4.0.30
RUN echo "\033[44;37m --------------------------------安裝php-------------------------------- \033[0m"
RUN apt-get -y update \
    && apt-get install -y software-properties-common \
    && LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php \
    && apt-get -y update\
    && apt-get install -y php7.2 \
    && apt-get install -y php7.2-fpm php7.2-mbstring php7.2-xmlrpc php7.2-soap php7.2-gd php7.2-xml php7.2-cli php7.2-zip \
    && apt-get install -y php7.2 php7.2-dev php7.2-xml -y --allow-unauthenticated \
    && apt-get -y remove --purge apache2 apache2-utils \
    && apt-get -y autoremove
RUN echo "\033[44;37m --------------------------------安裝php-mysql pdo-------------------------------- \033[0m"
RUN apt-get install -y php7.2-mysql
RUN sed -i '906s/;extension=pdo_mysql/extension=pdo_mysql/' /etc/php/7.2/fpm/php.ini
RUN echo "\033[44;37m --------------------------------config檔加密------------------------------- \033[0m"
RUN apt-get install -y openssl
RUN openssl enc -aes-128-cbc -in /var/www/spdpaas/config/constants.conf -out /var/www/spdpaas/config/encconstants.conf -pass pass:sapidotest2019
RUN echo "\033[44;37m --------------------------------https-------------------------------- \033[0m"
COPY ./copy_to_container/ssl /etc/nginx/ssl
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/nginx/ssl/nginx.key -out /etc/nginx/ssl/nginx.crt -config /etc/nginx/ssl/ssl.conf
RUN cp /etc/nginx/ssl/nginx.crt /usr/share/ca-certificates/
RUN sed -i '1926a;Yishan add\nopenssl.cafile=/etc/nginx/ssl/nginx.crt' /etc/php/7.2/fpm/php.ini
RUN echo "\033[44;37m --------------------------------增加uwsgi logrotate-------------------------------- \033[0m"
RUN chmod +x /init/uwsgi_logrotate.sh
RUN /bin/bash /init/uwsgi_logrotate.sh
RUN echo "\033[44;37m --------------------------------配置celery supervisor------------------------------- \033[0m"
RUN apt-get install -y supervisor
COPY ./copy_to_container/supervisor /etc/supervisor
RUN echo "\033[44;37m --------------------------------還原資料庫資料------------------------------- \033[0m"
RUN chown -R www-data:www-data /var/www
# RUN service nginx restart \
#     && service php7.2-fpm restart
#     # && service emperor.uwsgi restart \
RUN chmod +x /init/restoredb.sh
RUN /bin/bash /init/restoredb.sh
RUN echo "\033[44;37m --------------------------------OK------------------------------- \033[0m"
# CMD ["uwsgi","--ini","/etc/uwsgi/emperor.ini"]
EXPOSE 80
EXPOSE 443
EXPOSE 3687
CMD ["/bin/bash", "/start.sh"]
# CMD ["uwsgi", "-i", "/var/www/spdpaas/spdpaas_uwsgi.ini"]