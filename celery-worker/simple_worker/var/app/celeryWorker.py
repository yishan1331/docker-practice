from celery import Celery
import ConfigParser

from celeryConfig import BaseConfig as celeryConfig

def _readConfig():
    # print "#######_readConfig########"
    dicConfig = {}
    try:
        CONFPATH = "/var/app/config.conf"

        FILECONFIG = ConfigParser.ConfigParser()
        FILECONFIG.read(CONFPATH)

        dicConfig = {
            'celery_broker' : FILECONFIG.get('Celery', 'broker'),
            'postgres_ip' : FILECONFIG.get('Postgres', 'ip'),
            'postgres_port' : FILECONFIG.get('Postgres', 'port'),
            'postgres_user' : FILECONFIG.get('Postgres', 'user'),
            'postgres_pwd' : FILECONFIG.get('Postgres', 'pwd'),
            'redis_ip' : FILECONFIG.get('Redis', 'ip'),
            'redis_port' : FILECONFIG.get('Redis', 'port'),
            'redis_pwd' : FILECONFIG.get('Redis', 'pwd')
        }

        # print "~~~~dicConfig~~~~~"
        # print dicConfig

    except Exception as e:
        print "~~~~_readConfig error~~~~"
        print e
    finally:
        return dicConfig

dicConfig = _readConfig()

app = Celery("sapidoPaaS",
    broker=dicConfig.get("celery_broker"),
    include=["celeryApp.celeryTasks"])
app.config_from_object(celeryConfig)