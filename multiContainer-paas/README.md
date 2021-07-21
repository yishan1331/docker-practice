# How to use
##### 1. 需先設定連接三種資料庫的帳密&celery broker&寄送Eamil的設定(./.env)
* Mariadb 
    > 帳號：MYSQL_PAAS_USER
    > 密碼：MYSQL_PAAS_PASSWORD
* PostgreSQL 
    > 帳號：POSTGRES_PAAS_USER
    > 密碼：POSTGRES_PAAS_PASSWORD
* Redis
    > 密碼：REDIS_REQUIREPASS
    > 資料庫：REDIS_HOST_DB
* Celery(broker為redis)
    > 密碼：CELERY_BROKER_PASSWORD
    > 資料庫：CELERY_BROKER_DB
* Email
    > 主機：EMAIL_HOST
    > 帳號：EMAIL_USER
    > 密碼：EMAIL_PASSWORD
    > 預設收件人：EMAIL_RECIPIENT

##### 2. 複製整包資料夾(multiContainer-paas)至目標server
##### 3. 進入目標server的multiContainer-paas資料夾內
##### 4. 確定dockewr&docker-compose已經安裝
##### 5. docker-compose up -d

##### 6. sh copyvolumes.sh(此步驟需以root角色執行)

##### 7. multiContainer-paas服務啟動

<!-- > 若有重啟php service或celeryworker，需執行service_cron_start.sh
> ex: sh service_cron_start.sh php \ sh service_cron_start.sh celeryworker -->