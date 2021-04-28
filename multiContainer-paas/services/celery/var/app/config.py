import ConfigParser

CONFPATH = "/var/app/config.conf"

CONFIG = ConfigParser.ConfigParser()
CONFIG.read(CONFPATH)

config = {
    "celery_broker_ip":CONFIG.get('celery_broker', 'ip'),
    "celery_broker_port":CONFIG.get('celery_broker', 'port'),
    "celery_broker_pwd":CONFIG.get('celery_broker', 'password'),
    "redis_ip":CONFIG.get('redis', 'ip'),
    "redis_port":CONFIG.get('redis', 'port'),
    "redis_pwd":CONFIG.get('redis', 'password'),
    "postgres_ip":CONFIG.get('postgres', 'ip'),
    "postgres_port":CONFIG.get('postgres', 'port'),
    "postgres_user":CONFIG.get('postgres', 'user'),
    "postgres_pwd":CONFIG.get('postgres', 'password')
}
print "##########config#########"
print config

__all__ = ['config']