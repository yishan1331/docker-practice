# -*- coding: utf-8 -*-                                                                                      
import os
import redis
import time
from datetime import datetime, date, timedelta
import subprocess

def _readConfig():
    print "#######_readConfig########"
    dicConfig = {}
    try:
        CONFPATH = "/var/www/spdpaas/config/constants.conf"

        FILECONFIG = ConfigParser.ConfigParser()
        FILECONFIG.read(CONFPATH)

        dicConfig = {
            'celery_broker_ip': FILECONFIG.get('CeleryBroker', 'ip'),
            'celery_broker_port': FILECONFIG.get('CeleryBroker', 'port'),
            'celery_broker_password': FILECONFIG.get('CeleryBroker', 'password'),
            'celery_broker_db': FILECONFIG.get('CeleryBroker', 'db'),
            'email_recipient': FILECONFIG.get('Email', 'recipient'),
        }

        print "~~~~dicConfig~~~~~"
        print dicConfig

    except Exception as e:
        print "~~~~_readConfig error~~~~"
        print e
    finally:
        return dicConfig

dicConfig = _readConfig()

POOL = redis.ConnectionPool(host=dicConfig.get("celery_broker_ip"), port=dicConfig.get("celery_broker_port"), db=dicConfig.get("celery_broker_db"),password=dicConfig.get("celery_broker_password"))

dbRedis = redis.Redis(connection_pool=POOL)

queuelen = dbRedis.llen("L-queue1")

recipient = dicConfig.get("email_recipient")
subject = '緊急通知'
body = '"L-queue1"已經壅塞，長度：{} '.format(queuelen)

def send_message(recipient, subject, body):
    try:
        process = subprocess.Popen(['mail', '-s', subject, recipient],stdin=subprocess.PIPE)
        process.communicate(body)
        print "已寄緊急通知信"
    except Exception as error:
        print "error:",error

print "time:",datetime.now().strftime("%Y-%m-%d %H:%M:%S.%f")[::],"| this queuelen -> ",queuelen
if queuelen > 10000:
    send_message(recipient, subject, body)