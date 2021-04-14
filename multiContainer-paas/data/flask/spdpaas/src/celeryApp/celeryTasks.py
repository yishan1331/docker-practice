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
import os, time, sys
#wei@02262019
reload(sys)
sys.setdefaultencoding('utf-8')
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
from apiPortal import app, celery
from configuration.celeryConfig import readConfig
from modules import ConvertData, retrieve_database_exist, create_database
from globalvar import SERVERIP, SYSTEMLIST
#}}}

__all__ = ('celery_post_api_count_record', 'celery_trigger_specific_program', 'celery_send_email', 'apirecord_hash_num_init')

class DBTask(Task):
    # _session = None
    # _sess = None
    # _metadata = None
    # _dbEngine = None
    _system = None
    _systemSess = {}
    print "~~~~DBTask~~~~"

    def after_return(self, *args, **kwargs):
        print "~~~~~after_return~~~~~"
        print self._system
        print self._systemSess[self._system]
        if self._systemSess[self._system] is not None:
            print "^^^^^^^^^^^^^^^^"
            self._systemSess[self._system][0].close()
            self._systemSess[self._system][3].remove()
            self._systemSess[self._system][2].dispose()
            print "vvvvvvvvvvvvvvvv"

    # @property
    def session(self, system):
        # print "$$$$$$$$$$$$$$$$"
        # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
        # print system
        try:
            self._system = system
            print "self._systemSess -> ",self._systemSess
            print "system -> ",system

            # 03172021@Yishan 將不同system的db session存入dict，避免建立新的連接
            if (not self._systemSess.has_key(self._system)):
                from sqlalchemy import MetaData
                from sqlalchemy.orm import scoped_session, sessionmaker
                from sqlalchemy.engine import create_engine
                from modules import check_dbconnect_success
                
                dbUri = "postgresql+psycopg2://{}:{}@{}:{}/{}".format( \
                            app.dicConfig.get("DBPOSTGRESUser"),   \
                            app.dicConfig.get("DBPOSTGRESPassword"),   \
                            app.dicConfig.get("DBPOSTGRESIp"), \
                            app.dicConfig.get("DBPOSTGRESPort"),  \
                            "sapidoapicount_"+self._system.lower())
                print "@@@@@@dbUri@@@@@@@@"
                print dbUri
                _dbEngine = create_engine(dbUri,encoding='utf-8')
                print "111111111111111"

                _metadata = MetaData(bind=_dbEngine)
                print "222222222222222"
                _session = scoped_session(sessionmaker(autocommit=False, \
                                            autoflush=False, \
                                            bind=_dbEngine))
                print "333333333333333"

                check_status,check_result = check_dbconnect_success(_session, self._system)
                print "444444444444444"
                if not check_status: return None,None,check_result
                
                _sess = _session()
                self._systemSess[self._system] = [_sess,_metadata,_dbEngine,_session]

            return self._systemSess[self._system]
            # return self._sess,self._metadata,self._dbEngine
            
        except Exception as e:
            err_msg = app.catch_exception(e,sys.exc_info(),system)
            return None,None,err_msg

def print_mem():
    print 'Memory usage: %s (kb)' % resource.getrusage(resource.RUSAGE_SELF).ru_maxrss

@celery.task(base=DBTask, bind=True)
def celery_post_api_count_record(self, threaddata):
    from apiPortal import checkexisted_api_count_record_table, create_api_count_record_table
    import redis
    threaddata = ConvertData().convert(threaddata)
    print "~~~~threaddata~~~~"
    print self.request.id,datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
    print threaddata
    system = threaddata[0]
    if threaddata[0] == "test": system = "IOT"
    dbName = "sapidoapicount_"+system.lower()

    apimethod = threaddata[1]
    apipath = threaddata[2]
    status = 0 if threaddata[3] == "0" else 1
    apiTimeCost = threaddata[4]
    apicompletiontime = threaddata[5]

    # if system not in SYSTEMLIST[SERVERIP]: return
    
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
    print sessRaw,metaRaw,engineRaw
    # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
    # print "&&&&&&&&&&&&&&&&&&&&&&&&"
    if not sessRaw is None:
        try:
            dbRedis,_,_= app.getDbSessionType(system="PaaS",dbName=15,forRawData="redis")
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
                            print "====redis pipe error===="
                            print self.request.id,datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
                            count += 1
                            print count
                            if count == 5:
                                count = 0

                                redis_count_key = "_".join(redis_count_key.split("_")[0:3])
                                if hash_num < apirecord_hash_num:
                                    hash_num += 1
                                    redis_count_key += "_"+"#"*hash_num
                                else:
                                    hash_num = 0

                                print "@@@@@@@@@@redis_count_key@@@@@@@@@@"
                                print redis_count_key

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
            err_msg = app.catch_exception(e, sys.exc_info(), system)
            print "~~~~err_msg~~~~"
            print err_msg

        finally:
            print_mem()
        #     sessRaw.close()
        #     DbSessionRaw.remove()
        #     engineRaw.dispose()
    
    else:
        #Yishan 02062020 當postgresql連線數超過時先寫入txt
        with open("/var/www/spdpaas/doc/offlinemode_apirecord.txt",'r+') as x:
            fileaString = x.read()
            txt_success = int(fileaString[8])
            txt_fail =  int(fileaString[15])
            if not status:
                txt_fail += 1
                x.seek(15,0)
                x.write(txt_fail)
            else:
                txt_success +=1
                x.seek(8,0)
                x.write(txt_success)
#}}}

#=======================================================
# 觸發指定程式
# Date: 01252020@Yishan
#=======================================================
@celery.task()
def celery_trigger_specific_program(cmd,SYSTEM):
    try:        
        with open(os.devnull, 'w') as devnull:
            process = subprocess.check_output(cmd,shell=False,stderr=devnull)
    except Exception as e:
        print '~~~~~~celery_trigger_specific_program Exception~~~~~'
        print e
    
    # print_mem()

#=======================================================
# 發送郵件
# Date: 01252020@Yishan
#=======================================================
@celery.task()
def celery_send_email(selfUse,emailData):
    from features.emailHandle import EmailConfig
    send = EmailConfig(selfUse)
    #發送成功
    if send.sendemail(emailData):
        print("[{}] ".format(datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]),"To-do notification email sent successfully")
    #發送失敗
    else:
        print("[{}] ".format(datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]),"To-do notification email delivery failed")

#=======================================================
# Definition: 初始設定apirecord的hash numer(apirecord_hash_num)
# Date: 03302021@Yishan
#=======================================================
# {{{def apirecord_hash_num_init()
def apirecord_hash_num_init():
    try:
        dbRedis,_,_ = app.getDbSessionType(system="PaaS",dbName=15,forRawData="redis")
        if dbRedis is None:
            return
        
        #若key不存在，直接建立
        if not dbRedis.exists("apirecord_hash_num"):
            dbRedis.set("apirecord_hash_num", 10)

    except Exception as e:
        app.catch_exception(e, sys.exc_info(), "PaaS")