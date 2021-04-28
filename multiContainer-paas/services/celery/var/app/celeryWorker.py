# from celery import Celery

# from celeryConfig import BaseConfig as celeryConfig

# app = Celery("sapidoPaaS",
#     broker="redis://root:sapido@172.16.2.55:6379/15",
#     include=["celeryApp.celeryTasks"])
# app.config_from_object(celeryConfig)

from celery import Celery

from config import config
from celeryConfig import BaseConfig as celeryConfig

app = Celery("sapidoPaaS",
    broker="redis://root:{}@{}:{}/15".format(config["celery_broker_pwd"],config["celery_broker_ip"],config["celery_broker_port"]),
    include=["celeryApp.celeryTasks"])
app.config_from_object(celeryConfig)

import redis
POOL = redis.ConnectionPool(host=config["celery_broker_ip"], port=config["celery_broker_port"], db=15, password=config["celery_broker_pwd"])
dbRedis = redis.Redis(connection_pool=POOL)

if not dbRedis.exists("apirecord_hash_num"):
    dbRedis.set("apirecord_hash_num", 10)