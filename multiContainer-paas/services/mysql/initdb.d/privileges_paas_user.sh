mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e "create user '${MYSQL_PAAS_USER}'@'localhost' IDENTIFIED BY '${MYSQL_PAAS_PASSWORD}';"
mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e "grant ALL PRIVILEGES ON *.* TO '${MYSQL_PAAS_USER}'@'localhost';"
mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e "FLUSH PRIVILEGES;"