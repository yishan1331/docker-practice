# -*- coding: utf-8 -*-                                                                                      
import os
import redis
import time
from datetime import datetime, date, timedelta
import subprocess

POOL = redis.ConnectionPool(host='127.0.0.1', port=6379, db=15,password="sapido")

dbRedis = redis.Redis(connection_pool=POOL)

queuelen = dbRedis.llen("L-queue1")

recipient = 'yishanjob13@gmail.com'
subject = '緊急通知'
body = '"L-queue1"已經壅塞，長度：{} '.format(queuelen)

def send_message(recipient, subject, body):
    try:
        process = subprocess.Popen(['mail', '-s', subject, recipient],stdin=subprocess.PIPE)
    except Exception, error:
        print error
    process.communicate(body)

if queuelen > 10000:
    send_message(recipient, subject, body)