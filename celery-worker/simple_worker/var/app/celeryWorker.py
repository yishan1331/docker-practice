from celery import Celery

from celeryConfig import BaseConfig as celeryConfig
from config import config

print("~~~config~~~")
print(config)

app = Celery("sapidoPaaS",
    broker="redis://root:{}@{}:{}/{}".format(
        config["celery_broker_password"],
        config["celery_broker_ip"],
        config["celery_broker_port"],
        config["celery_broker_db"]
    ),
    include=["celeryApp.celeryTasks"])
app.config_from_object(celeryConfig)

import redis
POOL = redis.ConnectionPool(
    host=config["DBREDISIp"],
    port=config["DBREDISPort"],
    db=config["DBREDISDb"],
    password=config["DBREDISPassword"]
)
dbRedis = redis.Redis(connection_pool=POOL)

if not dbRedis.exists("apirecord_hash_num"):
    dbRedis.set("apirecord_hash_num", 10)