# -*- coding: utf-8 -*-
#Description
"""
==============================================================================
created    : 05/21/2020

Last update: 05/21/2020

Developer: Yishan Tsai 

Lite Version 1 @Yishan05212020

Filename: modules.py

Description: Common modules
==============================================================================
"""

#=======================================================
# System level modules
#=======================================================
#{{{
import sys
import traceback, re, time, json
from datetime import datetime, date, timedelta

#Yishan 11062019 引入Counter來找出list
from collections import Counter, Mapping, Iterable
from time import strftime 
from copy import deepcopy
from functools import wraps #Yishan@011112020 add for Decorators

from sqlalchemy import *
from sqlalchemy.sql import func
from sqlalchemy.dialects import mysql
from sqlalchemy.orm import scoped_session, sessionmaker
from sqlalchemy.engine import create_engine

#Yishan 07302020忽略(sqlalchemy.exc.SAWarning)
import warnings
from sqlalchemy import exc as sa_exc
warnings.filterwarnings('ignore', category=sa_exc.SAWarning)

#Yishan 11102020 加入redis in-memory db to store Validity period data
import redis
#}}}

#=======================================================
# convert data type
# Date: 09122019 Yishan
# 轉換data型態
#=======================================================
#{{{ class ConvertData()
class ConvertData():
    #def __init__(self,data):
    #    self.data = data

    def convert(self,data):
        self.data = data
        if isinstance(self.data, basestring):
            #進行過濾非法字元
            # return str(RegularExpression(self.data).re_sub())
            return str(self.data)
        elif isinstance(self.data, Mapping):
            return dict(map(self.convert, self.data.iteritems()))
        elif isinstance(self.data, Iterable):
            return type(self.data)(map(self.convert, self.data))
        else:
            return self.data
#}}}

#=======================================================
# Definition: 測試資料庫是否成功連上
# Date: 01072021@Yishan
#=======================================================
#{{{def deccheck_dbconnect_successorator_check_uri_parameter_exist(DbSession,metadata,engine)
def check_dbconnect_success(sess, system):
    """
    測試資料庫是否成功連上(若連上且查詢成功代表資料庫存在)

    Args:
        sess: database connect session
        system: 使用之系統名稱
    Returns:
        [0]: status(狀態，True/False)
        [1]: err_msg(返回訊息)
    """
    try:
        if not sess.execute("select 1 as is_alive"): raise Exception
        status = True
        err_msg = "ok"
    except Exception as e:
        err_msg = e
        status = False
    finally:
        sess.close()
        return status,err_msg

#=======================================================
# Definition: 檢查資料庫是否存在
# Date: 06092020@Yishan
#=======================================================
# {{{ def retrieve_database_exist(dbName)
def retrieve_database_exist(system, dbName="", forRawData="mysql"):
    """
    檢查資料庫是否存在

    Args:
        system: 使用之系統名稱
        dbName: 資料庫名稱
        forRawData: which kind of database
    Returns:
        [0]: status(存在與否，True/False)
        [1]: err_msg(返回訊息)
    """
    existed = True
    err_msg = "ok"
    try:
        DbSession,metadata,engine = getDbSessionType(dbName=dbName, forRawData=forRawData, system=system)
        if DbSession is None:
            existed = False
            err_msg = engine
    except Exception as e:
        existed = False
    finally:
        if 'DbSession' in locals().keys() and DbSession is not None:
            DbSession.remove()
            engine.dispose()

        return existed,err_msg
# }}}

#=======================================================
# Definition: 建立database
# Date: 06092020@Yishan
#=======================================================
# {{{ def create_database(dbName)
def create_database(system, dbName, forRawData="postgres"):
    status = True
    if forRawData != "postgres":
        return "Only PostgreSQL DataBase can create new database"

    try:
        if forRawData == "postgres":
            default_dbName = "postgres"
        else:
            default_dbName = dbName
            
        DbSession,_,engine = getDbSessionType(dbName=default_dbName,forRawData=forRawData,system=system)
        print("~~~~~~~~~~")
        print(DbSession)
        print(engine)
        if DbSession is None:
            #表示連接資料庫有問題
            return False,engine

        engcon = engine.connect()

        if forRawData == "postgres":
            dbName = dbName.lower()

        engcon.execute('commit')
        engcon.execute('create database {}'.format(dbName))
        engcon.execute('commit')
        err_msg="ok"
    
    except Exception as e:
        print("~~~~e~~~~")
        print(e)
        strExcInfo = str(sys.exc_info()[1][0])
        if "exists" in strExcInfo and dbName in strExcInfo:
            err_msg = "ok"
        else:
            err_msg = e
            status = False
    finally:
        if 'DbSession' in locals().keys() and DbSession is not None:
            engcon.close()
            DbSession.remove()
            engine.dispose()

    print("~~~~~~~create_database~~~~~~~")
    print(status)
    print(err_msg)
    return status,err_msg
# }}}

#=======================================================
# 建立新的countapitimes Table(年月為單位)
# Date: Yishan 09272019
#=======================================================
# {{{ def create_api_count_record_table(sessRaw, metaRaw, tbName, system, which, dbRedis=None):
def create_api_count_record_table(sessRaw, metaRaw, tbName, system, which, dbRedis=None):
    try:
        if which == "count":
            if dbRedis is not None:
                dbRedis.hmset(tbName,{"success_counts":0,"fail_counts":0,"success_averagetime":0,"upload_time":datetime.now().strftime("%Y-%m-%d %H:%M:%S")})
                print("~~~~tbName~~~~")
                print(tbName)
                dbRedis.expire(tbName,88200) #設定過期秒數1天又30分鐘
            else:
                new_table = Table(tbName, metaRaw, 
                    Column("success_counts",Integer),
                    Column("fail_counts",Integer),
                    Column("success_averagetime",Float),
                    Column("upload_time", TIMESTAMP(timezone=False),server_default=func.current_timestamp(6)) 
                )
                addData = {"success_counts":0,"fail_counts":0,"success_averagetime":0,"upload_time":datetime.now().strftime("%Y-%m-%d %H:%M:%S")}
        else:
            new_table = Table(tbName, metaRaw, 
                Column("method", VARCHAR(10),nullable=False), 
                Column("apipath", VARCHAR(256),nullable=False), 
                Column("status", Boolean,nullable=False), 
                Column("operation_time", Float,nullable=False),
                Column("completion_time", TIMESTAMP(timezone=False)),
                Column("upload_time", TIMESTAMP(timezone=False),server_default=func.current_timestamp(6)) 
            )

        if dbRedis is None:
            new_table.create()
            metaRaw.create_all()

            if which == "count":
                sessRaw.execute(new_table.insert().values(addData))
                sessRaw.commit()
        
    
    except Exception as e:
        print("~~~create_api_count_record_table error~~~~")
        print(e)
# }}}

#=======================================================
# 檢查countapitimes Table是否存在
# Date: Yishan 09272019
#=======================================================
#{{{ def checkexisted_api_count_record_table(sessRaw, metaRaw, dbName, tbName_count, system, dbRedis=None):
def checkexisted_api_count_record_table(sessRaw, metaRaw, dbName, tbName_count, system, dbRedis=None):
    from sqlalchemy.exc import InvalidRequestError
    tbName_record = tbName_count+"_record"

    try:
        if dbRedis is not None:
            redis_api_key = "api_"+system.lower()+"_"+time.strftime("%Y%m%d", time.localtime())
            if not dbRedis.exists(redis_api_key):
                create_api_count_record_table(sessRaw, metaRaw, redis_api_key, system, "count", dbRedis)

        try:
            #Yishan 10182019 check table existed with metaRaw.reflect()
            metaRaw.reflect(only=[tbName_count])
        
        except InvalidRequestError as error:
            print("~~~~error~~~~")
            print(error)
            if not re.search(r'Could not reflect',str(error)):
                return False

            create_api_count_record_table(sessRaw, metaRaw, tbName_count, system, "count")
        
        try:
            #Yishan 10182019 check table existed with metaRaw.reflect()
            metaRaw.reflect(only=[tbName_record])
        
        except InvalidRequestError as error:
            print("~~~~error~~~~")
            print(error)
            if not re.search(r'Could not reflect',str(error)):
                return False

            create_api_count_record_table(sessRaw, metaRaw, tbName_record, system, "record")
                
        return True
    except Exception as e:
        print("~~~~checkexisted_api_count_record_table error~~~~")
        print(e)
        return False
#}}}

#=======================================================
# DB Session Generation process  
#=======================================================
# {{{ def getDbSessionType()
def getDbSessionType(dbName="", forRawData="mysql", system=None, driver="pyodbc", echo=False):
    if system is None: return None,None,"No system"
    #POSTGRES
    PostgresqlUser = "DBPOSTGRESUser"
    PostgresqlPassword = "DBPOSTGRESPassword"
    PostgresqlIP = "DBPOSTGRESIp"
    PostgresqlPort = "DBPOSTGRESPort"

    #Redis
    RedisIp = "DBREDISIp"
    RedisPort = "DBREDISPort"
    RedisPassword = "DBREDISPassword"

    if forRawData == 'postgres':
        dbUri = "postgresql+psycopg2://{}:{}@{}:{}/{}".format(dicConfig.get("postgres_user"),dicConfig.get("postgres_pwd"),dicConfig.get("postgres_ip"),dicConfig.get("postgres_port"),dbName)

    elif forRawData == 'redis':
        try:
            #採用此方式connect無需再特地disconnect，會自動disconnect 
            #not need to do -> dbRedis.connection_pool.disconnect()
            POOL = redis.ConnectionPool(host="{}".format(dicConfig.get("redis_ip")),\
                                        port="{}".format(dicConfig.get("redis_port")),\
                                        db=dbName,\
                                        password="{}".format(dicConfig.get("redis_pwd")))
            dbRedis = redis.Redis(connection_pool=POOL)
            return dbRedis,None,None
        except Exception as e:
            return None,None,e

    try:
        if echo:
            dbEngine = create_engine(dbUri,encoding='utf-8',echo=True)
            # dbEngine = create_engine('mssql+pymssql://sd:DmakerSQL@2020@172.16.2.57:1433/sapidoERP?charset=utf8',encoding='utf-8',echo=True)
        else:
            dbEngine = create_engine(dbUri,encoding='utf-8')
        
        #Yishan@11242020 無須先engine.connect，當session execute時就會自動連接了
        # dbCon = dbEngine.connect()
        # dbCon.close()
        from sqlalchemy import MetaData
        metadata = MetaData(bind=dbEngine)
        dbSessionType = scoped_session(sessionmaker(autocommit=False, \
                                    autoflush=False, \
                                    bind=dbEngine))

        check_connect_success,check_result = check_dbconnect_success(dbSessionType(), system)
        if not check_connect_success: return None,None,check_result
        
        return dbSessionType,metadata,dbEngine
    
    except Exception as e:
        check_connect_success = False
        err_msg = e
        
        return None,None,err_msg
    
    finally:
        if 'dbEngine' in locals().keys() and dbEngine is not None and not check_connect_success:
            print("----------dispose dbEngine------------")
            dbSessionType.remove()
            dbEngine.dispose()
# }}}