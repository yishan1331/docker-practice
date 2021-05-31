1. copy this folder to target server
2. into this folder
3. docker-compose up -d
4. sh copyvolumes.sh

> 若有重啟php service或celeryworker，需執行service_cron_start.sh
> ex: sh service_cron_start.sh php \ sh service_cron_start.sh celeryworker