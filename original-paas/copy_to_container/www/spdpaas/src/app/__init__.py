# -*- coding: utf-8 -*-
#=======================================================
# System level modules
#=======================================================
#{{{
print "---------init.py-------------"
import sys, os
#wei@02262019
reload(sys)
sys.setdefaultencoding('utf-8')
import traceback, re, time, json
from datetime import datetime, date, timedelta

from flask import Flask, request, jsonify, Blueprint

import ConfigParser
import threading

from sqlalchemy.sql import func
from sqlalchemy.dialects import mysql
# from sqlalchemy.orm import mapper, sessionmaker, relationship, backref

from globalvar import *
# import web_utility
# import authentic_utility
#}}}

# print 'I am being imported by', sys._getframe(1).f_globals.get('__name__')

# 建立 thread lock
lock = threading.Lock()

def create_app():
    #Yishan 09092020 加入flask_socketio用於Dashboard logging modules
    from flask_cors import CORS
    from flask_socketio import SocketIO
    from flask_bootstrap import Bootstrap

    FRONTEND_FOLDER = os.path.join(os.getcwd(),'dashboard')
    appPaaS = Flask('sapidoPaaS',template_folder=FRONTEND_FOLDER,static_folder=os.path.join(FRONTEND_FOLDER,'static'))

    CORS(appPaaS,cors_allowed_origins="*")  
    socketio = SocketIO(appPaaS,cors_allowed_origins="*")
    # socketio.init_app(appPaaS,cors_allowed_origins="*")

    Bootstrap(appPaaS)

    return appPaaS,socketio
appPaaS,socketio = create_app()

#Yishan@07162020 added for logging module
from paasLogging import applogger
#服務啟動時就執行setting logging config，此時尚未有上下文所以須將app當參數帶過去
applogger(appPaaS)

#=======================================================
# ger ConfigParser and catch error
#=======================================================
#{{{
def _getConfigParserDetails(config,section,option):
    status = True
    try:
        err_msg = config.get(section,option)
    except (ConfigParser.NoOptionError,ConfigParser.NoSectionError) as error:
        status = False
        err_msg = error
    finally:
        return status,err_msg
#}}}

#=======================================================
# API service configuration processing  
#=======================================================
# {{{
def _readConfig():
    print "#######_readConfig########"
    dicConfig = {}
    try:
        # print "now in readConfig()"
        get_request_start_time = int(round(time.time()* 1000000))

        #print "start-readConfig"
        if not os.path.isfile('/var/www/spdpaas/config/deconstants_{}.conf'.format(str(get_request_start_time))):
            #print "readConfig-not exist"
            #Yishan 10152019 config解密
            with os.popen('/usr/bin/openssl enc -aes-128-cbc -d -in /var/www/spdpaas/config/encconstants.conf -out /var/www/spdpaas/config/deconstants_{}.conf -pass pass:sapidotest2019'.format(str(get_request_start_time))) as osdecrypt:
                #使os.popen執行完再往下執行
                osdecrypt.read()
        #else:
            #print "readConfig-exist"

        CONFPATH = "/var/www/spdpaas/config/deconstants_{}.conf".format(str(get_request_start_time))

        FILECONFIG = ConfigParser.ConfigParser()
        FILECONFIG.read(CONFPATH)
        LOGPATH = FILECONFIG.get('LOGPATH', 'session')
        KEY_LOC = FILECONFIG.get('KeyPath', 'key')
        CODE = FILECONFIG.get('Secret', 'code')
        AES_KEY = FILECONFIG.get('AES_KEY', 'key')

        #app.logging.info("config key_loc {}".format(KEY_LOC))

        TIMEOUTVALUE = FILECONFIG.getint('TimeOut', 'value')

        #Celery
        CELERY_BROKER = FILECONFIG.get('Celery', 'broker')
        CELERY_RESULT_BACKEND = FILECONFIG.get('Celery', 'result_backend')

        #PaaS預設資料庫的ip、port、user、password(MYSQL、POSTGRESQL、REDIS)
        #MYSQL
        DBMYSQLIp = FILECONFIG.get(CONFIG["SYSTEM"]["MYSQL"],'ip')
        DBMYSQLPort = FILECONFIG.get(CONFIG["SYSTEM"]["MYSQL"],'port')
        DBMYSQLUser = FILECONFIG.get(CONFIG["SYSTEM"]["MYSQL"],'user')
        DBMYSQLPassword = FILECONFIG.get(CONFIG["SYSTEM"]["MYSQL"],'password')
        #POSTGRESQL
        DBPOSTGRESIp = FILECONFIG.get(CONFIG["SYSTEM"]["POSTGRESQL"],'ip')
        DBPOSTGRESPort = FILECONFIG.get(CONFIG["SYSTEM"]["POSTGRESQL"],'port')
        DBPOSTGRESUser = FILECONFIG.get(CONFIG["SYSTEM"]["POSTGRESQL"],'user')
        DBPOSTGRESPassword = FILECONFIG.get(CONFIG["SYSTEM"]["POSTGRESQL"],'password')
        #REDIS
        DBREDISIp = FILECONFIG.get(CONFIG["SYSTEM"]["REDIS"],'ip')
        DBREDISPort = FILECONFIG.get(CONFIG["SYSTEM"]["REDIS"],'port')
        DBREDISPassword = FILECONFIG.get(CONFIG["SYSTEM"]["REDIS"],'password')

        DEFAULT_DB_CONFIG = {
            "DBMYSQLIp":DBMYSQLIp,
            "DBMYSQLPort":DBMYSQLPort,
            "DBMYSQLUser":DBMYSQLUser,
            "DBMYSQLPassword":DBMYSQLPassword
        }
        #Master PaaS feature
        status,result = _getConfigParserDetails(FILECONFIG,CONFIG["SYSTEM"]["PaaS"],'dbname')
        if status:
            DBMYSQLDbname_PaaS = result
        else:
            DBMYSQLDbname_PaaS = None

        # #Email feature
        # try:
        #     #Yishan@10272020 get office 365 mail server user,password
        #     EmailHost = CONFIG.get('Email', 'host')
        #     EmailUser = CONFIG.get('Email', 'user')
        #     EmailPassword = CONFIG.get('Email', 'password')
        # except ConfigParser.NoSectionError: #https://www.programcreek.com/python/example/1658/ConfigParser.NoSectionError
        #     EmailHost = None
        #     EmailUser = None
        #     EmailPassword = None
        
        #APIDOC feature
        status,result = _getConfigParserDetails(FILECONFIG,CONFIG["SYSTEM"]["APIDOC"],'dbname')
        if status:
            DBMYSQLDbname_APIDOC = result
        else:
            DBMYSQLDbname_APIDOC = None


        #Yishan@08072019 修改抓取資料庫基本資料方式
        diff_system = {}
        repeatconfig = ["ip","port","user","password","dbname"]
        #diff server ip
        for key,value in CONFIG[SERVERIP].items():
            for key2,value2 in value.items():
                if key2 not in DBCONFIG.keys():
                    for key3,value3 in CONFIG.items(key2):
                        diff_system[key2+key3.capitalize()] = value3
                else:
                    for j in range(len(value2)):
                        for i in repeatconfig:
                            status,result = _getConfigParserDetails(FILECONFIG,value2[j],i)
                            if j == 0:
                                this_key = DBCONFIG[key2]+i.capitalize()+"_"+key
                            else:
                                this_key = DBCONFIG[key2]+i.capitalize()+"_"+key+"_"+str(j+1)

                            if status:
                                diff_system[this_key] = result
                            else:
                                diff_system[this_key] = DEFAULT_DB_CONFIG[DBCONFIG[key2]+i.capitalize()]

        print "~~~~diff_system~~~~~"
        print diff_system

        dicConfig = {  'log_path': LOGPATH, \
            'key_loc': KEY_LOC, \
            'aes_key': AES_KEY,\
            'code'   : CODE,    \
            'timeout'  : TIMEOUTVALUE,  \
            'celery_broker'  : CELERY_BROKER,  \
            'celery_result_backend'  : CELERY_RESULT_BACKEND,  \
            'DBMYSQLIp_PaaS' : DBMYSQLIp, \
            'DBMYSQLPort_PaaS' : DBMYSQLPort,  \
            'DBMYSQLUser_PaaS' : DBMYSQLUser,  \
            'DBMYSQLPassword_PaaS' : DBMYSQLPassword,  \
            'DBMYSQLDbname_PaaS' : DBMYSQLDbname_PaaS,  \
            'DBMYSQLIp_APIDOC' : DBMYSQLIp, \
            'DBMYSQLPort_APIDOC' : DBMYSQLPort,  \
            'DBMYSQLUser_APIDOC' : DBMYSQLUser,  \
            'DBMYSQLPassword_APIDOC' : DBMYSQLPassword,  \
            'DBMYSQLDbname_APIDOC' : DBMYSQLDbname_APIDOC,  \
            'DBPOSTGRESIp' : DBPOSTGRESIp, \
            'DBPOSTGRESPort' : DBPOSTGRESPort,  \
            'DBPOSTGRESUser' : DBPOSTGRESUser,  \
            'DBPOSTGRESPassword' : DBPOSTGRESPassword,  \
            'DBREDISIp' : DBREDISIp,  \
            'DBREDISPort' : DBREDISPort,  \
            'DBREDISPassword' : DBREDISPassword,  \
        }

        dicConfig.update(diff_system)
        
        print "~~~~dicConfig~~~~~"
        print dicConfig

    except Exception as e:
        print "~~~~_readConfig error~~~~"
        print e
        err_msg = appPaaS.catch_exception(e,sys.exc_info(),"PaaS")

    finally:
        if os.path.isfile('/var/www/spdpaas/config/deconstants_{}.conf'.format(str(get_request_start_time))):
            #print "readConfig-exist"
            with os.popen('/bin/rm /var/www/spdpaas/config/deconstants_{}.conf'.format(str(get_request_start_time))) as osrm:
                osrm.read()
        #else:
            #print "readConfig-not exist"

        return dicConfig
# }}}
dicConfig = _readConfig()

from celeryApp.celeryBroker import make_celery
celery = make_celery(appPaaS,dicConfig)
print "~~~~celery~~~~"
print celery

if sys._getframe(1).f_globals.get('__name__') == "spdpaas":
    import oauth2.oauth2
    #在此建立oauth2所需的db_session
    OAUTH2_SESS,OAUTH2_DbSESSION,OAUTH2_ENGINE,OAUTH2_DEREDIS,authorization = oauth2.oauth2.config_oauth(appPaaS,dicConfig)
else:
    OAUTH2_SESS = None
    OAUTH2_DbSESSION = None
    OAUTH2_ENGINE = None
    OAUTH2_DEREDIS = None
    authorization = None
    import apiPortal

__all__ = [
    'sys', 'os', 'traceback', 're',
    'time', 'json', 'datetime', 'date', 'timedelta',
    'request', 'jsonify', 'Blueprint',
    'func', 'mysql','threading',
    'appPaaS', 'socketio', 'dicConfig', 'celery', 'lock',
    'authorization', 'OAUTH2_SESS', 'OAUTH2_DbSESSION',
    'OAUTH2_ENGINE', 'OAUTH2_DEREDIS',
    'globalvar'
]