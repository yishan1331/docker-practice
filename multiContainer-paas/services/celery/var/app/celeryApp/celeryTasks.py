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
import redis
from celery import Task
from celery.utils.log import get_task_logger
#}}}

#=======================================================
# User level modules
#=======================================================
#{{{
from config import config
from celeryWorker import app
from modules import ConvertData, retrieve_database_exist, create_database, getDbSessionType, checkexisted_api_count_record_table
#}}}

__all__ = ('celery_post_api_count_record')

logger = get_task_logger(__name__)

class DBTask(Task):
    _system = None
    _systemSess = {}
    logger.debug("~~~~DBTask~~~~")

    def after_return(self, *args, **kwargs):
        logger.debug("~~~~after_return~~~~")
        logger.debug(self._system)
        logger.debug(self._systemSess[self._system])
        if self._systemSess[self._system] is not None:
            logger.debug("^^^^^^^^^^^^^^^^")
            self._systemSess[self._system][0].close()
            self._systemSess[self._system][3].remove()
            self._systemSess[self._system][2].dispose()
            logger.debug("^^^^^^^^^^^^^^^^")

    # @property
    def session(self, system):
        try:
            self._system = system
            logger.debug("~~~~self._systemSess~~~")
            logger.debug(self._systemSess)
            logger.debug("~~~~system~~~~")
            logger.debug(system)

            # 03172021@Yishan 將不同system的db session存入dict，避免建立新的連接
            if (not self._systemSess.has_key(self._system)):
                from sqlalchemy import MetaData
                from sqlalchemy.orm import scoped_session, sessionmaker
                from sqlalchemy.engine import create_engine
                from modules import check_dbconnect_success
                
                dbUri = "postgresql+psycopg2://{}:{}@{}:{}/{}".format(\
                            config["DBPOSTGRESUser"],\
                            config["DBPOSTGRESPassword"],\
                            config["DBPOSTGRESIp"],\
                            config["DBPOSTGRESPort"],\
                            "sapidoapicount_"+self._system.lower())
                logger.debug("~~~~dbUri~~~~")
                logger.debug(dbUri)
                _dbEngine = create_engine(dbUri,encoding='utf-8')
                _metadata = MetaData(bind=_dbEngine)
                _session = scoped_session(sessionmaker(autocommit=False, \
                                            autoflush=False, \
                                            bind=_dbEngine))
                check_status,check_result = check_dbconnect_success(_session, self._system)
                if not check_status: return None,None,check_result
                
                _sess = _session()
                self._systemSess[self._system] = [_sess,_metadata,_dbEngine,_session]

            return self._systemSess[self._system]
            
        except Exception as e:
            err_msg = e
            return None,None,err_msg

def print_mem():
    logger.debug('Memory usage: %s (kb)' % resource.getrusage(resource.RUSAGE_SELF).ru_maxrss)
    # print('Memory usage: %s (kb)' % resource.getrusage(resource.RUSAGE_SELF).ru_maxrss)

@app.task(base=DBTask, bind=True)
def celery_post_api_count_record(self, threaddata):
    threaddata = ConvertData().convert(threaddata)
    logger.debug("~~~~threaddata~~~~")
    logger.debug(datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::])
    logger.debug(self.request.id)
    logger.debug(threaddata)
    system = threaddata[0]
    dbName = "sapidoapicount_"+system.lower()

    apimethod = threaddata[1]
    apipath = threaddata[2]
    status = 0 if threaddata[3] == "0" else 1
    apiTimeCost = threaddata[4]
    apicompletiontime = threaddata[5]

    tbName_count = "api_"+system.lower()+"_"+time.strftime("%Y%m", time.localtime())
    redis_count_key = "api_"+system.lower()+"_"+time.strftime("%Y%m%d", time.localtime())
    if not retrieve_database_exist(system, dbName=dbName, forRawData="postgres")[0]:
        create_database(system, dbName, forRawData="postgres")
    
    sessionResult = self.session(system)
    sessRaw = sessionResult[0]
    metaRaw = sessionResult[1]
    engineRaw = sessionResult[2]
    # sessRaw,metaRaw,engineRaw = self.session(system)
    # print "&&&&&&&&&&&&&&&&&&&&&&&&"
    logger.debug(sessRaw)
    logger.debug(metaRaw)
    logger.debug(engineRaw)
    # print(sessRaw,metaRaw,engineRaw)
    # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
    # print "&&&&&&&&&&&&&&&&&&&&&&&&"
    if not sessRaw is None:
        try:
            dbRedis,_,_= getDbSessionType(system=system,dbName=15,forRawData="redis")
            if dbRedis is None:
                return

            apirecord_hash_num = int(dbRedis.get("apirecord_hash_num"))

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
                            logger.debug("====redis pipe error====")
                            logger.debug(datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::])
                            logger.debug(self.request.id)
                            # print("====redis pipe error====")
                            # print(self.request.id,datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::])
                            count += 1
                            logger.debug(count)
                            # print(count)
                            if count == 5:
                                count = 0

                                redis_count_key = "_".join(redis_count_key.split("_")[0:3])
                                if hash_num < apirecord_hash_num:
                                    hash_num += 1
                                    redis_count_key += "_"+"#"*hash_num
                                else:
                                    hash_num = 0

                                logger.debug("@@@@@@@@@@redis_count_key@@@@@@@@@@")
                                logger.debug(redis_count_key)
                                # print("@@@@@@@@@@redis_count_key@@@@@@@@@@")
                                # print(redis_count_key)

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
            logger.error("~~~~e~~~~")
            logger.error(e)
            # print("~~~~e~~~~")
            # print(e)

        finally:
            print_mem()