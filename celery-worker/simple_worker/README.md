# How to use
##### 1. 需先設定連接資料庫的config檔(/var/app/config.conf)
##### 2. 設定每日定時將指定system之api統計資料新增回Master server(/.env -> SYSTEMLIST)，此值為該Master server可允許的所有system list
##### 3. 設定Celery的Log Level(/.env -> CELERYLOG)
##### 4. build images
`docker build --rm -t celery-worker .`
##### 5. run container
`docker run -dit --name helper -p 7788:6379 --restart=always --env-file=.env -v helper-redis:/var/lib/redis celery-worker`
指令 | 意義
| -------- | -------- 
| –restart=always | 機器重啟後Container自動重啟（預設是關閉）    