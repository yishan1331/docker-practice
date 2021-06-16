#-*- coding: utf-8 -*-                                                                                                        
import os
import redis
import time
import ConfigParser
from datetime import datetime, date, timedelta

def _readConfig():
    print "#######_readConfig########"
    dicConfig = {}
    try:
        CONFPATH = "/var/app/config.conf"

        FILECONFIG = ConfigParser.ConfigParser()
        FILECONFIG.read(CONFPATH)

        dicConfig = {
            'celery_broker_ip': FILECONFIG.get('CeleryBroker', 'ip'),
            'celery_broker_port': FILECONFIG.get('CeleryBroker', 'port'),
            'celery_broker_password': FILECONFIG.get('CeleryBroker', 'password'),
            'celery_broker_db': FILECONFIG.get('CeleryBroker', 'db'),
            "DBPOSTGRESIp":FILECONFIG.get('DataBasePostgresql', 'ip'),
            "DBPOSTGRESPort":FILECONFIG.get('DataBasePostgresql', 'port'),
            "DBPOSTGRESUser":FILECONFIG.get('DataBasePostgresql', 'user'),
            "DBPOSTGRESPassword":FILECONFIG.get('DataBasePostgresql', 'password')
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

import globalvar
tempsystemlist = globalvar.SYSTEMLIST[globalvar.SERVERIP]
tempsystemlist.append("PaaS")

for i in tempsystemlist:
    yesterday = datetime.today() + timedelta(-1)
    # 03112021@Yishan 增加分流api record keys
    yesterday_redis_keys = "api_"+i.lower()+"_"+yesterday.strftime('%Y%m%d')+"*"
    print(yesterday_redis_keys)
    print(dbRedis.keys(yesterday_redis_keys))
    for yesterday_redis_key in dbRedis.keys(yesterday_redis_keys):
        if dbRedis.exists(yesterday_redis_key):
            success_counts = dbRedis.hget(yesterday_redis_key,"success_counts")
            success_averagetime = dbRedis.hget(yesterday_redis_key,"success_averagetime")
            fail_counts = dbRedis.hget(yesterday_redis_key,"fail_counts")
            upload_time = dbRedis.hget(yesterday_redis_key,"upload_time")
            print("~~~this count~~~")
            print(success_counts,fail_counts,success_averagetime,upload_time)

            dbName = "sapidoapicount_"+i.lower()
            print("~~~dbName~~~")
            print(dbName)
            try:
                from sqlalchemy import MetaData, Table
                from sqlalchemy.orm import scoped_session, sessionmaker
                from sqlalchemy.engine import create_engine
                from modules import check_dbconnect_success
                
                dbUri = "postgresql+psycopg2://{}:{}@{}:{}/{}".format(dicConfig.get("DBPOSTGRESUser"),dicConfig.get("DBPOSTGRESPassword"),dicConfig.get("DBPOSTGRESIp"),dicConfig.get("DBPOSTGRESPort"),dbName)
                print("@@@@@@dbUri@@@@@@@@")
                print(dbUri)
                dbEngine = create_engine(dbUri,encoding='utf-8')

                metadata = MetaData(bind=dbEngine)
                session = scoped_session(sessionmaker(autocommit=False, \
                                            autoflush=False, \
                                            bind=dbEngine))
                
                sessRaw = session()

                yesterday_api_count_table = Table("api_"+i.lower()+"_"+yesterday.strftime('%Y%m') , metadata, autoload=True)

                print("~~~~old counts~~~~")
                print(sessRaw.query(yesterday_api_count_table).all())

                updateSql = "update {yesterday_api_count_table} set success_counts=(select success_counts+{success_counts} from {yesterday_api_count_table}),\
                    success_averagetime=(SELECT CASE WHEN success_counts = 0 THEN round({success_averagetime}::numeric,3) \
                        ELSE round(((success_counts*success_averagetime+{success_averagetime})/(success_counts+{success_counts}))::numeric,3) END from {yesterday_api_count_table}),\
                    fail_counts=(select fail_counts+{fail_counts} from {yesterday_api_count_table}),\
                    upload_time='{upload_time}'".format(yesterday_api_count_table=yesterday_api_count_table, success_counts=success_counts, fail_counts=fail_counts, success_averagetime=success_averagetime, upload_time=upload_time)
                print("~~~~updateSql~~~~")
                print(updateSql)

                sessRaw.execute(updateSql)
                sessRaw.commit()

                print("~~~~new counts~~~~")
                print(sessRaw.query(yesterday_api_count_table).all())
                
                err_msg = "ok"

            except Exception as e:
                print("~~~~Exception~~~~")
                print(e,sys.exc_info())
                
            finally:
                if 'session' in locals().keys():
                    sessRaw.close()
                    session.remove()
                    dbEngine.dispose()