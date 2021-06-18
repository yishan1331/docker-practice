#-*- coding: utf-8 -*-                                                                                                        
import redis
from datetime import datetime, timedelta

from config import config

POOL = redis.ConnectionPool(
    host=config["DBREDISIp"],
    port=config["DBREDISPort"],
    db=config["DBREDISDb"],
    password=config["DBREDISPassword"]
)

dbRedis = redis.Redis(connection_pool=POOL)

import globalvar
tempsystemlist = globalvar.SYSTEMLIST[globalvar.SERVERIP]
tempsystemlist.append("PaaS")
print(tempsystemlist)

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
                
                dbUri = "postgresql+psycopg2://{}:{}@{}:{}/{}".format(
                                    config["DBPOSTGRESUser"],
                                    config["DBPOSTGRESPassword"],
                                    config["DBPOSTGRESIp"],
                                    config["DBPOSTGRESPort"],
                                    dbName)
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