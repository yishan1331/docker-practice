# from celery import Celery

# from celeryConfig import BaseConfig as celeryConfig

# app = Celery("sapidoPaaS",
#     broker="redis://root:sapido@172.16.2.55:6379/15",
#     include=["celeryApp.celeryTasks"])
# app.config_from_object(celeryConfig)

from celery import Celery

# from app import config
from celeryConfig import BaseConfig as celeryConfig

# import ConfigParser

# CONFPATH = "/var/app/config.conf"

# CONFIG = ConfigParser.ConfigParser()
# CONFIG.read(CONFPATH)

# config = {
#     "celery_broker_ip":CONFIG.get('celery_broker', 'ip'),
#     "celery_broker_port":CONFIG.get('celery_broker', 'port'),
#     "celery_broker_pwd":CONFIG.get('celery_broker', 'password'),
#     "redis_ip":CONFIG.get('redis', 'ip'),
#     "redis_port":CONFIG.get('redis', 'port'),
#     "redis_pwd":CONFIG.get('redis', 'password'),
#     "postgres_ip":CONFIG.get('postgres', 'ip'),
#     "postgres_port":CONFIG.get('postgres', 'port'),
#     "postgres_user":CONFIG.get('postgres', 'user'),
#     "postgres_pwd":CONFIG.get('postgres', 'password')
# }

config = {
    "celery_broker_ip":"192.168.88.71",
    "celery_broker_port":"6379",
    "celery_broker_pwd":"sapido",
    "redis_ip":"redis",
    "redis_port":"6379",
    "redis_pwd":"sapido",
    "postgres_ip":"192.168.88.71",
    "postgres_port":"5680",
    "postgres_user":"sapidopostgres",
    "postgres_pwd":"Touspourun_3M"
}

app = Celery("sapidoPaaS",
    broker="redis://root:{}@{}:{}/15".format(config["celery_broker_pwd"],config["celery_broker_ip"],config["celery_broker_port"]),
    include=["celeryApp.celeryTasks"])
app.config_from_object(celeryConfig)

import redis
POOL = redis.ConnectionPool(host='redis', port=6379, db=15,password="sapido")
dbRedis = redis.Redis(connection_pool=POOL)

if not dbRedis.exists("apirecord_hash_num"):
    dbRedis.set("apirecord_hash_num", 10)