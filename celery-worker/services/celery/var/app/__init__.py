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
    "redis_ip":"127.0.0.1",
    "redis_port":"6379",
    "redis_pwd":"sapido",
    "postgres_ip":"192.168.88.71",
    "postgres_port":"5680",
    "postgres_user":"sapidopostgres",
    "postgres_pwd":"Touspourun_3M"
}