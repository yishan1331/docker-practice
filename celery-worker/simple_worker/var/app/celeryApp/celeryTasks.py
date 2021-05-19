# -*- coding: utf-8 -*-
"""
==============================================================================
created    : 02/17/2021
Last update: 02/17/2021
Developer: Yishan Tsai
Lite Version 2 @Yishan08212019
API version 1.0

Filename: celeryTasks.py

Description: for celery tasks
==============================================================================
"""
#=======================================================
# System level modules
#=======================================================
#{{{
import time
from datetime import datetime
import subprocess #Yishan 05212020 subprocess 取代 os.popen
from sqlalchemy import Table
import resource
from celery import Task
#}}}

#=======================================================
# User level modules
#=======================================================
#{{{
from celeryWorker import app, dicConfig
from modules import ConvertData, retrieve_database_exist, create_database, getDbSessionType
#}}}

__all__ = ('celery_post_api_count_record')

class DBTask(Task):
    _session = None
    _sess = None
    _metadata = None
    _dbEngine = None
    print("~~~~DBTask~~~~")

    def after_return(self, *args, **kwargs):
        if self._session is not None:
            self._sess.close()
            self._session.remove()
            self._dbEngine.dispose()

    # @property
    def session(self, system):
        try:
            if self._session is None:
                from sqlalchemy import MetaData
                from sqlalchemy.orm import scoped_session, sessionmaker
                from sqlalchemy.engine import create_engine
                from modules import check_dbconnect_success
                
                dbUri = "postgresql+psycopg2://{}:{}@{}:{}/{}".format(dicConfig.get("postgres_user"),dicConfig.get("postgres_pwd"),dicConfig.get("postgres_ip"),dicConfig.get("postgres_port"),"sapidoapicount_"+system.lower())
                print("@@@@@@dbUri@@@@@@@@")
                print(dbUri)
                self._dbEngine = create_engine(dbUri,encoding='utf-8')
                print("111111111111111")

                self._metadata = MetaData(bind=self._dbEngine)
                print("222222222222222")
                self._session = scoped_session(sessionmaker(autocommit=False, \
                                            autoflush=False, \
                                            bind=self._dbEngine))
                print("333333333333333")

                check_status,check_result = check_dbconnect_success(self._session, system)
                print("444444444444444")
                if not check_status: return None,None,check_result
                
                self._sess = self._session()

            return self._sess,self._metadata,self._dbEngine
            
        except Exception as e:
            err_msg = e
            return None,None,err_msg

def print_mem():
    print('Memory usage: %s (kb)' % resource.getrusage(resource.RUSAGE_SELF).ru_maxrss)

@app.task(base=DBTask, bind=True)
def celery_post_api_count_record(self, threaddata):
    from modules import checkexisted_api_count_record_table
    import redis
    threaddata = ConvertData().convert(threaddata)
    print("~~~~threaddata~~~~")
    print(self.request.id,datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::])
    print(threaddata)
    system = threaddata[0]
    if threaddata[0] == "test": system = "IOT"
    dbName = "sapidoapicount_"+system.lower()

    apimethod = threaddata[1]
    apipath = threaddata[2]
    status = 0 if threaddata[3] == "0" else 1
    apiTimeCost = threaddata[4]
    apicompletiontime = threaddata[5]

    tbName_count = "api_"+system.lower()+"_"+time.strftime("%Y%m", time.localtime())
    redis_count_key = "api_"+system.lower()+"_"+time.strftime("%Y%m%d", time.localtime())
    if not retrieve_database_exist(system, dicConfig, dbName=dbName, forRawData="postgres")[0]:
        create_database(system, dbName, dicConfig, forRawData="postgres")
    
    sessRaw,metaRaw,engineRaw = self.session(system)
    if not sessRaw is None:
        try:
            dbRedis,_,_= getDbSessionType(dicConfig, system="PaaS",dbName=15,forRawData="redis")
            if dbRedis is None:
                return

            apirecord_hash_num = 10

            checkexistedResult = checkexisted_api_count_record_table(sessRaw, metaRaw, dbName, tbName_count, system, dbRedis)
        
            if checkexistedResult:
                this_api_record_table = Table(tbName_count+"_record" , metaRaw, autoload=True)
                sessRaw.execute(this_api_record_table.insert().values({"method":apimethod,"apipath":apipath,"status":bool(status),"operation_time":apiTimeCost,"completion_time":apicompletiontime}))
                sessRaw.commit()

                with dbRedis.pipeline() as pipe:
                    count = 0
                    hash_num = 0
                    while True:
                        try:
                            # print "====start redis pipe while===="
                            # print self.request.id,datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
                            # 監聽redis api today key 
                            pipe.watch(redis_count_key)

                            success_counts = int(pipe.hget(redis_count_key,"success_counts"))
                            fail_counts = int(pipe.hget(redis_count_key,"fail_counts"))
                            success_averagetime = float(pipe.hget(redis_count_key,"success_averagetime"))

                            # 开始事务
                            pipe.multi()
                            # 执行操作
                            if not status:
                                pipe.hincrby(redis_count_key, "fail_counts", amount=1)
                            else:
                                success_totaltime = (success_averagetime * success_counts)+apiTimeCost
                                this_averagetime = success_totaltime/(success_counts+1)
                                # this_averagetime = float('%.4f' % (success_totaltime/(success_counts+1)))
                                pipe.hmset(redis_count_key,{"success_counts":success_counts+1,"success_averagetime":this_averagetime,"upload_time":datetime.now().strftime("%Y-%m-%d %H:%M:%S")})
                            # 执行事务
                            pipe.execute()
                            break
                        except redis.WatchError:
                            # 事务执行过程中，如果数据被修改，则抛出异常，程序可以选择重试或退出
                            print("====redis pipe error====")
                            print(self.request.id,datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::])
                            count += 1
                            print(count)
                            if count == 5:
                                count = 0

                                redis_count_key = "_".join(redis_count_key.split("_")[0:3])
                                if hash_num < apirecord_hash_num:
                                    hash_num += 1
                                    redis_count_key += "_"+"#"*hash_num
                                else:
                                    hash_num = 0

                                print("@@@@@@@@@@redis_count_key@@@@@@@@@@")
                                print(redis_count_key)

                                if not dbRedis.exists(redis_count_key):
                                    dbRedis.hmset(redis_count_key,{"success_counts":0,"fail_counts":0,"success_averagetime":0,"upload_time":datetime.now().strftime("%Y-%m-%d %H:%M:%S")})
                                    dbRedis.expire(redis_count_key,88200) #設定過期秒數1天又30分鐘
                            continue
                        finally:
                            # 取消对键的监视
                            pipe.unwatch()
                            # 因为 redis-py 在执行 WATCH 命令期间，会将流水线与单个连接进行绑定
                            # 所以在执行完 WATCH 命令之后，必须调用 reset() 方法将连接归还给连接池
                            pipe.reset() # 重置管道，为重试做准备
                    # print "====after redis pipe while===="
                    # print self.request.id,datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]

        except Exception as e:
            print("~~~~err_msg~~~~")
            print(err_msg)

        finally:
            print_mem()