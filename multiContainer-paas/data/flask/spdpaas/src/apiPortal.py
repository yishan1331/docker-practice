# -*- coding: utf-8 -*-
#API Portal Description
"""
==============================================================================
created    : 03/23/2017

Last update: 03/31/2021

Developer: Wei-Chun Chang 

Lite Version 1 @Yishan08032019

Filename: apiPortal.py

Description: The script is the portal for the API requests from client apps

Total = 1 APIs
==============================================================================
"""
#=======================================================
# System level modules
#=======================================================
#{{{
import sys, os
#wei@02262019
reload(sys)
sys.setdefaultencoding('utf-8')
import traceback, re, time, json
from datetime import datetime, date, timedelta

#from flasgger.utils import swag_from
import ConfigParser

from flask import Flask, request, jsonify, Blueprint, render_template, make_response
from flask_bootstrap import Bootstrap

#Yishan 09092020 加入flask_socketio用於Dashboard logging modules
from flask_cors import CORS
from flask_socketio import SocketIO

from sqlalchemy import *
from sqlalchemy import func  #Yishan 05292019 without ignoring cases
from sqlalchemy.orm import scoped_session, sessionmaker
from sqlalchemy.engine import create_engine

#Yishan 07302020忽略(sqlalchemy.exc.SAWarning)
import warnings
from sqlalchemy import exc as sa_exc
warnings.filterwarnings('ignore', category=sa_exc.SAWarning)

#Yishan 11102020 加入redis in-memory db to store Validity period data
import redis
import threading
import Queue #python2是import Queue，python3是import queue
#}}}

#=======================================================
# User-defined modules
#=======================================================
#{{{
import globalvar
# import web_utility
# import authentic_utility
#}}}

#=======================================================
# API route on different files
# adding to test blueprint
#=======================================================
#{{{
FRONTEND_FOLDER = os.path.join(os.getcwd(),'dashboard')
app = Flask('sapidoPaaS',template_folder=FRONTEND_FOLDER,static_folder=os.path.join(FRONTEND_FOLDER,'static'))

CORS(app,cors_allowed_origins="*")  
socketio = SocketIO(app,cors_allowed_origins="*")
# socketio.init_app(app,cors_allowed_origins="*")

# 建立 thread lock
lock = threading.Lock()

Bootstrap(app)

#Yishan@07162020 added for logging module
from paasLogging import applogger
#服務啟動時就執行setting logging config，此時尚未有上下文所以須將app當參數帶過去
applogger(app)
# paasLogging.app = app

import modules #Yishan@05212020 added for common modules
modules.app = app

#==========================features==========================
#Yishan@08292019 added for transition databases management
from features.commonUse.transitionDBHandle import TRANSITIONDB_API
app.register_blueprint(TRANSITIONDB_API)

#Yishan@03262020 added for update data from remotedb management
from features.commonUse.remoteDBHandle import REMOTEDB_API
app.register_blueprint(REMOTEDB_API)

#Yishan@12172020 added for in memory databases management
from features.commonUse.inMemoryDBHandle import INMEMORYDB_API
app.register_blueprint(INMEMORYDB_API)

#Yishan@0326 added for multiple type of databases management
from features.commonUse.mixingDBHandle import MIXINGDB_API
app.register_blueprint(MIXINGDB_API)

#Wei@03232017 added for user management
from features.specificTable.userHandle import USER_API
app.register_blueprint(USER_API)

#Yishan@05282020 added for department management
from features.specificTable.departmentHandle import DEPARTMENT_API
app.register_blueprint(DEPARTMENT_API)
#=======================================================

#==========================APIDOC==========================
#Yishan@08292019 added for commonuse management
from APIDOC.commonuseHandle import COMMONUSE_APIDOC_API
app.register_blueprint(COMMONUSE_APIDOC_API)
#=======================================================

#==========================SOCKETIO==========================
#Yishan@09102020 added for socketio management
import features.socketHandle
#=======================================================

#==========================DASHBOARD==========================
#Yishan@010272020 added for dashboard modules
from features.dashboard import DASHBOARD_API
app.register_blueprint(DASHBOARD_API)
#=======================================================
#}}}

#=======================================================
# for a thread to check license every N seconds 
#=======================================================
# {{{ check_Lic(arg):A Thread for license check
import thread #Python3以後thread被廢棄，改為_thread或是使用threading
#wei@2017, use for license check
app.licCheck = 0 
#with schedule
def check_Lic(arg):
    #app.licCheck = os.system("/var/www/config/ezclient")
    while True:
        #app.licCheck = os.system("/var/www/config/ezclient")
        app.licCheck = 0
        print '\n[{}]:License checking result: {}\n'.format(time.ctime(),app.licCheck)
        time.sleep(arg)

app.check_Lic = check_Lic
#initiate a thread to do the job, check license every 12 hours
thread.start_new_thread(check_Lic, (43200, ))
#====================================================
# }}}

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
    try:
        # print "now in readConfig()"
        get_request_start_time = int(round(time.time()* 1000000))

        if hasattr(app,'dicConfig'):
            print "$$$$$$app.dicConfig$$$$$"
            print app.dicConfig
            return app.dicConfig

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

        CONFIG = ConfigParser.ConfigParser()
        CONFIG.read(CONFPATH)
        LOGPATH = CONFIG.get('LOGPATH', 'session')
        KEY_LOC = CONFIG.get('KeyPath', 'key')
        CODE = CONFIG.get('Secret', 'code')
        AES_KEY = CONFIG.get('AES_KEY', 'key')

        #app.logging.info("config key_loc {}".format(KEY_LOC))

        TIMEOUTVALUE = CONFIG.getint('TimeOut', 'value')

        #Celery
        CELERY_BROKER = CONFIG.get('Celery', 'broker')
        CELERY_RESULT_BACKEND = CONFIG.get('Celery', 'result_backend')

        #PaaS預設資料庫的ip、port、user、password(MYSQL、POSTGRESQL、REDIS)
        #MYSQL
        DBMYSQLIp = CONFIG.get(globalvar.CONFIG["SYSTEM"]["MYSQL"],'ip')
        DBMYSQLPort = CONFIG.get(globalvar.CONFIG["SYSTEM"]["MYSQL"],'port')
        DBMYSQLUser = CONFIG.get(globalvar.CONFIG["SYSTEM"]["MYSQL"],'user')
        DBMYSQLPassword = CONFIG.get(globalvar.CONFIG["SYSTEM"]["MYSQL"],'password')
        #POSTGRESQL
        DBPOSTGRESIp = CONFIG.get(globalvar.CONFIG["SYSTEM"]["POSTGRESQL"],'ip')
        DBPOSTGRESPort = CONFIG.get(globalvar.CONFIG["SYSTEM"]["POSTGRESQL"],'port')
        DBPOSTGRESUser = CONFIG.get(globalvar.CONFIG["SYSTEM"]["POSTGRESQL"],'user')
        DBPOSTGRESPassword = CONFIG.get(globalvar.CONFIG["SYSTEM"]["POSTGRESQL"],'password')
        #REDIS
        DBREDISIp = CONFIG.get(globalvar.CONFIG["SYSTEM"]["REDIS"],'ip')
        DBREDISPort = CONFIG.get(globalvar.CONFIG["SYSTEM"]["REDIS"],'port')
        DBREDISPassword = CONFIG.get(globalvar.CONFIG["SYSTEM"]["REDIS"],'password')

        DEFAULT_DB_CONFIG = {
            "DBMYSQLIp":DBMYSQLIp,
            "DBMYSQLPort":DBMYSQLPort,
            "DBMYSQLUser":DBMYSQLUser,
            "DBMYSQLPassword":DBMYSQLPassword
        }
        #Master PaaS feature
        status,result = _getConfigParserDetails(CONFIG,globalvar.CONFIG["SYSTEM"]["PaaS"],'dbname')
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
        status,result = _getConfigParserDetails(CONFIG,globalvar.CONFIG["SYSTEM"]["APIDOC"],'dbname')
        if status:
            DBMYSQLDbname_APIDOC = result
        else:
            DBMYSQLDbname_APIDOC = None


        #Yishan@08072019 修改抓取資料庫基本資料方式
        diff_system = {}
        repeatconfig = ["ip","port","user","password","dbname"]
        #diff server ip
        for key,value in globalvar.CONFIG[globalvar.SERVERIP].items():
            for key2,value2 in value.items():
                if key2 not in globalvar.DBCONFIG.keys():
                    for key3,value3 in CONFIG.items(key2):
                        diff_system[key2+key3.capitalize()] = value3
                else:
                    for j in range(len(value2)):
                        for i in repeatconfig:
                            status,result = _getConfigParserDetails(CONFIG,value2[j],i)
                            if j == 0:
                                this_key = globalvar.DBCONFIG[key2]+i.capitalize()+"_"+key
                            else:
                                this_key = globalvar.DBCONFIG[key2]+i.capitalize()+"_"+key+"_"+str(j+1)

                            if status:
                                diff_system[this_key] = result
                            else:
                                diff_system[this_key] = DEFAULT_DB_CONFIG[globalvar.DBCONFIG[key2]+i.capitalize()]

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
        if not hasattr(app,'dicConfig'):
            setattr(app,'dicConfig', dicConfig)
            print "*****app.dicConfig already setting*****"
        
        print "~~~~dicConfig~~~~~"
        print dicConfig

    except Exception as e:
        err_msg = app.catch_exception(e,sys.exc_info(),"PaaS")

    finally:
        if os.path.isfile('/var/www/spdpaas/config/deconstants_{}.conf'.format(str(get_request_start_time))):
            #print "readConfig-exist"
            with os.popen('/bin/rm /var/www/spdpaas/config/deconstants_{}.conf'.format(str(get_request_start_time))) as osrm:
                osrm.read()
        #else:
            #print "readConfig-not exist"
# }}}
#服務啟動時就執行readConfig
_readConfig()

from celeryApp.celeryBroker import make_celery
celery = make_celery(app)
print "~~~~celery~~~~"
print celery

#====================================================
# 須等待celery初始化app後才能載入之modules
#====================================================
#Yishan@05282020 added for customized management 專門客製api
from features.customizedHandle import CUSTOMIZED_API,iot_redis_device_keys_init
app.register_blueprint(CUSTOMIZED_API)

#Wei@03232017 added for sensor management
from features.specificTable.sensorHandle import SENSOR_API
app.register_blueprint(SENSOR_API)

#Yishan@05282020 added for relatedpg management
from features.specificTable.relatedpgHandle import RELATEDPG_API
app.register_blueprint(RELATEDPG_API)

#Yishan@010272020 added for email modules
from features.emailHandle import EMAIL_API
app.register_blueprint(EMAIL_API)
# from features.emailHandle import EMAIL_API, check_todolist_deadline
#=======================================================

#=======================================================
# root URI, use to test API services are alive 
#=======================================================
# {{{ app.route("/")
@app.route("/")
def homePage():
    #print '\nIn sessionMgr, Lic: {}\n'.format(app.licCheck)
    dicRet = app.preProcessRequest(request, system="PaaS")
    #reserved for user ID check
    #reUser_id = request.args.get("uid")
    mesg = "<h1 style='color:blue'>sapido-License check failed!</h1>"
    if(app.licCheck == 0 ):
        mesg = "<h1 style='color:blue'>sapido-PaaS!</h1>"

    dicRet["message"] = mesg    
    dicRet["APIS"] = "{} {}".format(request.method,request.path) 
    dicRet["Response"] = "ok" 
    return jsonify( **dicRet)
# }}}
#=======================================================

#test for queue
# my_queue = Queue.Queue()
# @app.route("/TestThreadQueue/<number>")
# def test_thread_queue(number):
#     import threading

#     # 建立 lock
#     lock = threading.Lock()

#     print "now queue size -> ",my_queue.qsize()
#     # global my_queue
#     for i in range(int(number)):
#         my_queue.put("Data %d" % i)

#     print "this pid -> ",os.getpid()
#     print "now queue size -> ",my_queue.qsize()

#     my_worker1 = modules._WorkerThreadQueue(my_queue, 1, lock, 3)
#     my_worker2 = modules._WorkerThreadQueue(my_queue, 2, lock, 2)

#     my_worker1.start()
#     my_worker2.start()

#     my_worker1.join()
#     my_worker2.join()
#     print "now queue size -> ",my_queue.qsize()
#     print "Done."

#     return jsonify( **{"test":"ok"})

#=======================================================
# CommonUse
# special API to query time, 
#=======================================================
# {{{ app.route('/api/PaaS/1.0/CommonUse/Time', methods = ['GET']), 
@app.route('/api/PaaS/1.0/Time',  methods = ['GET'])
def get_time():
    #{{{APIINFO
    '''
    {
        "API_name":"GET /api/1.0/Time",
        "API_application":"查詢現在時間",
        "API_message_parameters":{"time":"string"},
        "API_example":{
            "OperationTime": "0.000",
            "BytesTransferred": 72,
            "time": "2019-08-08 10:21:40",
            "Response": "ok",
            "APIS": "GET /api/1.0/Time"
        }
    }
    '''
    #}}}
    err_msg = "ok"
    dicRet = app.preProcessRequest(request, system="PaaS")
    err_msg = dicRet["Response"]
    try:
        dicRet['time'] = datetime.now().strftime("%Y-%m-%d %H:%M:%S.%f")[::]
        err_msg = "ok" 

    except Exception as e:
        err_msg = app.catch_exception(e,sys.exc_info(),"PaaS")

    dicRet["Response"] = err_msg
    return jsonify( **dicRet)
# }}}

#=======================================================
# Http Request preprocess and post process including 
# 1. first_time  
# 2. before each  
# 3. post process
#=======================================================
# {{{ def preProcessRequest(request,companyUuid=None,dappId=None)
def preProcessRequest(request,system=""):
    err_msg = "ok"
    # print "..............................................................................................."
    # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
    # print "~~~~os.getpid()~~~~"
    # print os.getpid()
    targetApi = "{} {}".format(request.method,request.path)
    #logging.getLogger('fappYott').info("__[0824_request]: {}".format(request))

    #Wei@04132017 block for temperally not usage    
    #if companyUuid:
    #    #check special char in companyUUID
    #    ics = set('[`~!@#$%^&*()_+-={}<>,.?";:|\']+$').intersection(companyUuid)
    #    if ics:
    #        err_msg = "company_uuid={}, /w invalid chars {}".format(companyUuid,ics)

    #if dappId:
    #    #check special char in dappID
    #    ics = set('`~/\?%*:|"<> ').intersection(dappId)
    #    if ics:
    #        err_msg = "dapp_uuid={}, /w invalid chars {}".format(dappId,ics)

    #    #if err_msg == "ok":
    #    """ preflight requests issue, http://jiraccma.itri.org.tw:8080/browse/SAPIDOSYSTEM-23
    #    while handle DEL_BULKSAPIDOSYSTEM from javascript client code,
    #    flask found that `request.args` contained nothing,
    #    but expected values found at `request.form`
    #    and also `request.querystring` contain nothing
    #    """
    #W@03092017 block for testing new feature
    #if request.args:
    #    reqArgOk,err_msg = app.checkIfRequestArgsValid(request.args)
    #else:
    #    reqArgOk,err_msg = app.checkIfRequestArgsValid(request.form)
    #if err_msg == "ok":
    #reqSigOk,err_msg = app.verifyRequestSignature(request)

    dicRet = {}
    dicRet["APIS"] = targetApi
    dicRet["Response"] = err_msg
    dicRet["System"] = system
    return dicRet
# }}}
app.preProcessRequest = preProcessRequest

# {{{ @app.before_first_request
@app.before_first_request
def before_first_request():
    pass
# }}}

# {{{ @app.before_request
@app.before_request
def per_request_preprocess():
    if(app.licCheck != 0): #error
        print "Warning: License expired/License file missing......"
        return "License expired or failed"
    else:
        #if re.search('^\/v2\.\d+\/',request.path):
        #W@03092017, adding for all requests to include time cost
        setattr(request,"request_start_time_",time.time())
    # print "{} | {}".format(os.getpid(),datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::])
    #wei@03152017 test request
    #app.logging.error("In preProcess: request UID: {}".format(request.args.get("uid")))

    # http://stackoverflow.com/questions/8685678/cors-how-do-preflight-an-httprequest
    # This is to work for the preflight and 2-stage processing to pass the OPTIONs in the first flight
    #dicRet = { "Response" : "ok" }
    #Response = make_response(json.dumps(dicRet))
    #Response.headers.set('Access-Control-Allow-Origin', '*')
    #Response.headers.set('Access-Control-Allow-Methods', 'POST, OPTIONS, GET, DELETE')
    #Response.headers.set('Access-Control-Allow-Headers',
    #            request.headers.get('Access-Control-Request-Headers', 'Authorization' ))

    if request.method == 'OPTIONS':
        dicRet = { "Response" : "ok" }
        Response = make_response(json.dumps(dicRet))
        Response.headers.set('Access-Control-Allow-Origin', '*')
        Response.headers['Access-Control-Allow-Credentials'] = 'true'
        Response.headers.set('Access-Control-Allow-Methods', 'POST, OPTIONS, GET, DELETE')
        #wei@04282017 for LAN design
        Response.headers.set('Access-Control-Allow-Headers',
        request.headers.get('Access-Control-Request-Headers', 'Authorization' ))

        return Response
# }}}

# {{{ @app.teardown_appcontext
@app.teardown_appcontext
def teardown(exc=None):
    # print "@@@@@@@teardown_appcontext@@@@@"
    # print exc
    # if exc is None:
    #     dbSessionType.commit()
    # else:
    #     dbSessionType.rollback()
    # dbSessionType.remove()

    global OAUTH2_SESS
    global OAUTH2_DbSESSION
    global OAUTH2_ENGINE
    global OAUTH2_DEREDIS
    #在teardown_appcontext清空oauth2建立的db_session，以防session sleep a lot
    OAUTH2_SESS.close()
    OAUTH2_DbSESSION.remove()
    OAUTH2_ENGINE.dispose()
    OAUTH2_DEREDIS.connection_pool.disconnect()
#}}}

# {{{ @app.after_request
@app.after_request
def per_request_postprocess(Response):
    # this request is for returning testing string or pure string only APIs 
    # needs to define the key words to distinguish different APIs
    #if not re.search('^\/v2\.\d+\/',request.path):
    #    return Response
    try:
        dicRet = json.loads(Response.data)
        #wei@05022017 for CORS
        Response.headers.set('Access-Control-Allow-Origin', '*')
        #Response.headers['Access-Control-Allow-Credentials'] = 'true'
        Response.headers.set('Access-Control-Allow-Methods', 'POST, OPTIONS, GET, DELETE')

        nowTime = datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]

        if hasattr(request,"request_start_time_"):
            timeCost = time.time()-request.request_start_time_
            dic = { "OperationTime": "{:.3f}".format(timeCost),
                    "THISTIME":nowTime,
                    "BytesTransferred":len(Response.data)}
            # http://stackoverflow.com/questions/22274137/how-do-i-alter-a-Response-in-flask-in-the-after-request-function
            dicRet.update(dic)
            Response.set_data(json.dumps(dicRet))

        if dicRet.has_key('Response') and dicRet.has_key('System'):
            if dicRet.get('Response') != "ok":
                threaddata = [dicRet.get('System'), request.method, request.path, "0", timeCost, nowTime]
            else:
                threaddata = [dicRet.get('System'), request.method, request.path, "1", timeCost, nowTime]

            #暫時先排除為PaaS的api
            # if (dicRet.get('System') != "PaaS" and dicRet.get('System') is not None):
            if dicRet.get('System') is not None:
                # thread.start_new_thread(celery_post_api_count_record, (threaddata, ))

                #Yishan@12212020 修改thread寫法->threading
                # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
                # if re.search(r'./2.0/myps/Sensor/Rows/.*',request.path):
                    # print "~~~~~trigger start~~~~~~"
                    # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
                    # celery_post_api_count_record.delay(threaddata)
                # if re.search(r'/test/postrows',str(request.path)) or re.search(r'/api/test/1.0/rd/CommonUse/SpecificKey/mes_device_status_',str(request.path)) or re.search(r'/api/test/1.0/rd/CommonUse/Hash/Keys/SpecificField',str(request.path)):
                # if request.path in ("/test/postrows/f11","/api/test/1.0/rd/CommonUse/SpecificKey/mes_device_status_*","/api/test/1.0/rd/CommonUse/Hash/Keys/SpecificField"):
                    from celeryApp.celeryTasks import celery_post_api_count_record
                    celery_post_api_count_record.apply_async(args=(threaddata,), routing_key='low', queue="L-queue1")
                    
                    # worker = ApiRecordWorkerThread(os.getpid(), lock, post_api_count_record, threaddata)
                    # worker.start()

                    # print "@@@@@@@@@@@@@@@",datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
                # print "Done.",datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]

            #traceback.print_exc(file=sys.stdout)

        # if hasattr(request,"request_start_time_"):
        #     apiTimeCost = time.time()-request.request_start_time_
        #     dic = { "OperationTime": "{:.3f}".format(apiTimeCost),
        #             "BytesTransferred":len(Response.data)}
        #     # http://stackoverflow.com/questions/22274137/how-do-i-alter-a-Response-in-flask-in-the-after-request-function
        #     dicRet.update(dic)
        #     Response.set_data(json.dumps(dicRet))

        #     # #Yishan 1220 記錄所有使用者使用API的情形(apipath,OperationTime,fail/success,usetime)
        #     # if dicRet.get('Response') != "ok":
        #     #     Status = "Fail"
        #     # else:
        #     #     Status = "Success"
        #     # thread.start_new_thread(Post_apirecord, ([Status,dicRet.get('APIS'),apiTimeCost], ))
        #     # divider = "================================================================================="
        #     # with open("/var/www/spdpaas/doc/apirecord.txt", "a") as apirecord:
        #     #     if dicRet.get('Response') != "ok":
        #     #         Status = "Fail"
        #     #     else:
        #     #         Status = "Success"
        #     #     apirecord.write("UseTime : {}({})\nAPI Path : {}\nOperationTime : {:.3f}\nStatus : {}\n{}\n".format(datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::],int(round(time.time()* 1000)),dicRet.get('APIS'),apiTimeCost,Status,divider))

    except Exception as e:
        print request.path,Response.status_code,Response.status
        regexDict = {
            "APIDOC":r'edoc',
            "DASHBOARD":r'dashboard'
        }
        searchpathDict = {
            "APIDOC":False,
            "DASHBOARD":False
        }
        if Response.status != "404 NOT FOUND":
            for logtype in regexDict.keys():
                # print "~~~~logtype~~~~"
                # print logtype
                if re.search(regexDict[logtype],request.path):
                    searchpathDict[logtype] = True
            # print "~~~~searchpathDict~~~~"
            # print searchpathDict
            searchPath_faviconico = re.search(r'favicon.ico',request.path) #排除favicon.ico
            if searchPath_faviconico is None:
                if not (searchpathDict["APIDOC"] or searchpathDict["DASHBOARD"]):
                # if not searchPath_apidoc:
                    strExcInfo = "'{} {}' exception\n".format(request.method, request.path)
                    strExcInfo += "resp data: {}\n".format(Response.data)
                    strExcInfo += str(sys.exc_info())
                    for i in range(len(searchpathDict.values())):
                        # print "~~~~searchpathDict.values()[i]~~~~~"
                        # print searchpathDict.values()[i]
                        # print "~~~~searchpathDict.keys()[i]~~~~~"
                        # print searchpathDict.keys()[i]
                        if searchpathDict.values()[i]:
                            err_msg = app.catch_exception(e,sys.exc_info(),searchpathDict.keys()[i])

                    print "---------error----------"
                    print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
                    print sys.stdout
                    print traceback
                    print request.path
                    print "=====threading detail=====",os.getpid()
                    print threading.active_count() #用來查看目前有多少個線程
                    # print threading.enumerate() #目前使用線程的資訊
                    print threading.current_thread().name #可以用來查看你在哪一個執行緒當中
                    print
                    traceback.print_exc(file=sys.stdout)
                    #thread.start_new_thread(celery_post_api_count_record, (threaddata, ))
    #wei@05022017 debug
    #app.logging.error("In post-request: {}".format(Response.headers))

    # print "~~~~~~Return.~~~~~~~",datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
    return Response
# }}}
class ApiRecordWorkerThread(threading.Thread):
    def __init__(self, pid, lock, func, threadData):
        threading.Thread.__init__(self)
        self.pid = pid
        self.func = func
        self.lock = lock
        self.threadData = threadData

    def run(self):
        # 取得 lock
        self.lock.acquire()
        print "=====================ApiRecord====================="
        print "------Lock-----",self.pid
        print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::],self.pid
        # print "Lock acquired by pid %d" % self.pid
        print "------threading detail------",os.getpid()
        print threading.active_count() #用來查看目前有多少個線程
        print threading.current_thread().name #可以用來查看你在哪一個執行緒當中

        # # 不能讓多個執行緒同時進的工作
        # print "Worker %d" % (self.num)
        # time.sleep(1)
        self.func(self.threadData)

        # 釋放 lock
        self.lock.release()
        print "------released Lock-----",self.pid
        # print "Lock released by pid %d" % self.pid
        print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::],self.pid
        print "------threading detail------",os.getpid()
        print threading.active_count() #用來查看目前有多少個線程
        print threading.current_thread().name #可以用來查看你在哪一個執行緒當中
        print "=====================ApiRecord end====================="
        print 

class ApiRecordWorkerThread_(threading.Thread):
    def __init__(self, pid, lock, func):
        threading.Thread.__init__(self)
        self.pid = pid
        self.func = func
        self.lock = lock

    def run(self):
        # # 取得 lock
        # self.lock.acquire()
        # print "\n=====================ApiRecord====================="
        # print "------Lock-----",self.pid
        # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::],self.pid
        # # print "Lock acquired by pid %d" % self.pid
        # print "------threading detail------",self.pid
        # print threading.active_count() #用來查看目前有多少個線程
        # print threading.current_thread().name #可以用來查看你在哪一個執行緒當中

        # # 不能讓多個執行緒同時進的工作
        # print "Worker %d" % (self.num)
        # time.sleep(1)
        self.func()

        # # 釋放 lock
        # self.lock.release()
        # print "------released Lock-----",self.pid
        # # print "Lock released by pid %d" % self.pid
        # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::],self.pid
        # print "------threading detail------",self.pid
        # print threading.active_count() #用來查看目前有多少個線程
        # print threading.current_thread().name #可以用來查看你在哪一個執行緒當中
        # print "=====================ApiRecord end====================="
        # print 

#=======================================================
# 計算各api使用紀錄->apirecord.txt
# Date: Yishan 09082020 
#=======================================================
#{{{ def Post_apirecord(threaddata):
def Post_apirecord(threaddata):
    divider = "================================================================================="
    status = threaddata[0]
    apipath = threaddata[1]
    apiTimeCost = threaddata[2]
    with open("/var/www/spdpaas/doc/apirecord.txt", "a") as apirecord:
        apirecord.write("RecordTime : {}({})\nAPI Path : {}\nOperationTime : {:.3f}\nStatus : {}\n{}\n".format(datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::],int(round(time.time()* 1000)),apipath,apiTimeCost,status,divider))
#}}}

#=======================================================
# 計算各系統api使用次數&詳細資訊
# Date: Yishan 09262019 
#=======================================================
#{{{
def post_api_count_record(threaddata, which="record"):
    threaddata = modules.ConvertData().convert(threaddata)
    print "~~~~threaddata~~~~"
    print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
    print threaddata
    system = threaddata[0]
    if threaddata[0] == "test": system = "IOT"
    dbName = "sapidoapicount_"+system.lower()

    apimethod = threaddata[1]
    apipath = threaddata[2]
    status = 0 if threaddata[3] == "0" else 1
    apiTimeCost = threaddata[4]
    apicompletiontime = threaddata[5]

    if system not in globalvar.SYSTEMLIST[globalvar.SERVERIP]: return
    
    tbName_count = "api_"+system.lower()+"_"+time.strftime("%Y%m", time.localtime())
    if not modules.retrieve_database_exist(system, dbName=dbName, forRawData="postgres")[0]:
        modules.create_database(system, dbName, forRawData="postgres")
    
    DbSessionRaw,metaRaw,engineRaw = app.getDbSessionType(dbName=dbName,forRawData="postgres",system=system)
    if not DbSessionRaw is None:
        try:
            sessRaw = DbSessionRaw()

            checkexistedResult = checkexisted_api_count_record_table(sessRaw, metaRaw, dbName, tbName_count, system)
        
            if checkexistedResult:
                # this_api_count_table = Table(tbName_count , metaRaw, autoload=True)

                # if not status:
                #     failcounts += 1
                #     updateSql = this_api_count_table.update().values(fail_counts=failcounts,upload_time=apicompletiontime)
                # else:
                #     success_totaltime = success_averagetime * successcounts
                #     success_totaltime += apiTimeCost

                #     successcounts += 1
                #     averagetime = float('%.3f' % success_totaltime)/successcounts
                    
                #     updateSql = "update {tbName_count} set success_counts=(select success_counts+1 from {tbName_count}),\
                #         success_averagetime=(SELECT CASE WHEN success_counts = 0 THEN round({apiTimeCost}::numeric,3) \
                #             ELSE round(((success_counts*success_averagetime+{apiTimeCost})/(success_counts+1))::numeric,3) END from {tbName_count}),\
                #         upload_time='{apicompletiontime}'".format(tbName_count=tbName_count, apiTimeCost=apiTimeCost, apicompletiontime=apicompletiontime)
                # sessRaw.execute(updateSql)

                if which == "record":
                    this_api_record_table = Table(tbName_count+"_record" , metaRaw, autoload=True)
                    sessRaw.execute(this_api_record_table.insert().values({"method":apimethod,"apipath":apipath,"status":bool(status),"operation_time":apiTimeCost,"completion_time":apicompletiontime}))
                    sessRaw.commit()

        except Exception as e:
            err_msg = app.catch_exception(e,sys.exc_info(),system)

        finally:
            sessRaw.close()
            DbSessionRaw.remove()
            engineRaw.dispose()
            # print "~~~~~Post_countapitimes結束~~~~~~"
            # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
    
    else:
        #Yishan 02062020 當postgresql連線數超過時先寫入txt
        with open("offlinemode_apirecord.txt",'r+') as x:
            fileaString = x.read()
            txt_success = int(fileaString[8])
            txt_fail =  int(fileaString[15])
            if status == "0":
                txt_fail += 1
                x.seek(15,0)
                x.write(txt_fail)
            else:
                txt_success +=1
                x.seek(8,0)
                x.write(txt_success)
#}}}

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
                print "~~~~tbName~~~~"
                print tbName
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
        err_msg = app.catch_exception(e,sys.exc_info(),system)
# }}}

#=======================================================
# 檢查countapitimes Table是否存在
# Date: Yishan 09272019
#=======================================================
#{{{ def checkexisted_api_count_record_table(sessRaw, metaRaw, dbName, tbName_count, system, dbRedis=None):
def checkexisted_api_count_record_table(sessRaw, metaRaw, dbName, tbName_count, system, dbRedis=None):
    from sqlalchemy.exc import InvalidRequestError
    # successcounts = 0
    # failcounts = 0
    # success_averagetime = 0
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
            print "~~~~error~~~~"
            print error
            if not re.search(r'Could not reflect',str(error)):
                app.catch_exception(e,sys.exc_info(),system)
                return False

            create_api_count_record_table(sessRaw, metaRaw, tbName_count, system, "count")
        
        try:
            #Yishan 10182019 check table existed with metaRaw.reflect()
            metaRaw.reflect(only=[tbName_record])
        
        except InvalidRequestError as error:
            print "~~~~error~~~~"
            print error
            if not re.search(r'Could not reflect',str(error)):
                app.catch_exception(e,sys.exc_info(),system)
                return False

            create_api_count_record_table(sessRaw, metaRaw, tbName_record, system, "record")
                
        return True
    except Exception as e:
        app.catch_exception(e,sys.exc_info(),system)
        return False
#}}}

#=======================================================
# DB Session Generation process  
#=======================================================
# {{{ def getDbSessionType()
def getDbSessionType(dbName="", forRawData="mysql", system=None, specified=1, driver="pyodbc", echo=False):

    if system is None: return None,None,"No system"

    suffix = system
    try:
        if int(specified) > 1:
            suffix = system+"_"+str(specified)
    except Exception as e:
        return None,None,"指定資料庫必須為數字，數字內容請詢問系統維護者"

    #MYSQL
    MysqlUser = "DBMYSQLUser_"+suffix
    MysqlPassword = "DBMYSQLPassword_"+suffix
    MysqlIP = "DBMYSQLIp_"+suffix
    MysqlPort = "DBMYSQLPort_"+suffix
    MysqlDbname = "DBMYSQLDbname_"+suffix
    
    #POSTGRES
    PostgresqlUser = "DBPOSTGRESUser"
    PostgresqlPassword = "DBPOSTGRESPassword"
    PostgresqlIP = "DBPOSTGRESIp"
    PostgresqlPort = "DBPOSTGRESPort"

    #Redis
    RedisIp = "DBREDISIp"
    RedisPort = "DBREDISPort"
    RedisPassword = "DBREDISPassword"

    #Mssql
    MssqlUser = "DBMSSQLUser_"+suffix
    MssqlPassword = "DBMSSQLPassword_"+suffix
    MssqlIP = "DBMSSQLIp_"+suffix
    MssqlPort = "DBMSSQLPort_"+suffix
    MssqlDbname = "DBMSSQLDbname_"+suffix

    try:
        if forRawData == 'mysql':
            dbUri = 'mysql+pymysql://{}:{}@{}:{}/{}?charset=utf8mb4'.format( \
                                    app.dicConfig.get(MysqlUser),   \
                                    app.dicConfig.get(MysqlPassword),   \
                                    app.dicConfig.get(MysqlIP), \
                                    app.dicConfig.get(MysqlPort),  \
                                    app.dicConfig.get(MysqlDbname))

        elif forRawData == 'postgres':
            dbUri = "postgresql+psycopg2://{}:{}@{}:{}/{}".format( \
                                    app.dicConfig.get(PostgresqlUser),   \
                                    app.dicConfig.get(PostgresqlPassword),   \
                                    app.dicConfig.get(PostgresqlIP), \
                                    app.dicConfig.get(PostgresqlPort),  \
                                    # app.dicConfig.get(RawDbname))
                                    dbName)

        elif forRawData == 'mssql':
            if driver == "pyodbc":
                dbUri = 'mssql+pyodbc://{}:{}@{}:{}/{}?driver=ODBC+Driver+17+for+SQL+Server'.format( \
                                app.dicConfig.get(MssqlUser),   \
                                app.dicConfig.get(MssqlPassword),   \
                                app.dicConfig.get(MssqlIP), \
                                app.dicConfig.get(MssqlPort),  \
                                app.dicConfig.get(MssqlDbname))
                                # dbName)
            else:
                dbUri = 'mssql+pymssql://{}:{}@{}:{}/{}?charset=utf8'.format( \
                                app.dicConfig.get(MssqlUser),   \
                                app.dicConfig.get(MssqlPassword),   \
                                app.dicConfig.get(MssqlIP), \
                                app.dicConfig.get(MssqlPort),  \
                                app.dicConfig.get(MssqlDbname))
                                # dbName)
        elif forRawData == 'redis':
            try:
                #採用此方式connect無需再特地disconnect，會自動disconnect 
                #not need to do -> dbRedis.connection_pool.disconnect()
                POOL = redis.ConnectionPool(host=app.dicConfig.get(RedisIp),\
                                            port=app.dicConfig.get(RedisPort),\
                                            db=dbName,\
                                            password=app.dicConfig.get(RedisPassword))
                dbRedis = redis.Redis(connection_pool=POOL)
                return dbRedis,None,None
            except Exception as e:
                err_msg = app.catch_exception(e,sys.exc_info(),system)
                return None,None,err_msg
    except Exception as e:
        err_msg = app.catch_exception(e,sys.exc_info(),system)
        return None,None,err_msg

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

        doLoggerHandler = True
        if forRawData == 'postgres' and dbName == "paas_dashboard":
            doLoggerHandler = False
            
        check_connect_success,check_result = modules.check_dbconnect_success(dbSessionType(), system, doLoggerHandler=doLoggerHandler)
        if not check_connect_success: return None,None,check_result
        
        return dbSessionType,metadata,dbEngine
    
    except Exception as e:
        check_connect_success = False
        err_msg = app.catch_exception(e, sys.exc_info(), system)
        if re.search(r'None.',dbUri):
            return None,None,"資料庫連結url有錯誤"

    # except:
    #     import sys, traceback
    #     # traceback.print_exc(file=sys.stdout)
    #     print "~~~~ sys.exc_info()~~~~"
    #     print sys.exc_info()
    #     strExcInfo = str(sys.exc_info()[1][0])
    #     print "~~~~strExcInfo~~~~~"
    #     print strExcInfo
    #     #wei@03232017 solve the resource leakage
    #     if not ("exists" in strExcInfo and dbName in strExcInfo):
    #         app.logger_handler(system,'Error', sys.exc_info())
        
        return None,None,err_msg
    
    finally:
        if 'dbEngine' in locals().keys() and dbEngine is not None and not check_connect_success:
            print "----------dispose dbEngine------------"
            dbSessionType.remove()
            dbEngine.dispose()
# }}}
app.getDbSessionType = getDbSessionType

#=======================================================
# Definition: 初始建立有關oauth2的資料表(存在mysql->paas_dashboard)
# Date: 12142020@Yishan
#=======================================================
#{{{ def oauth2_table_init()
def oauth2_table_init():
    from oauth2.models import Base
    try:
        DbSession,_,engine= app.getDbSessionType(system="PaaS")
        if DbSession is None:
            print "~~~~~db connect fail before_first_request~~~~"
            print engine
            return

        Base.metadata.create_all(engine)

    except Exception as e:
        print "~~~~Exception before_first_request~~~~"
        print e
        err_msg = app.catch_exception(e,sys.exc_info(),"PaaS")
        print err_msg

    finally:
        if 'DbSession' in locals().keys() and DbSession is not None:
            DbSession.remove()
            engine.dispose()
#}}}
oauth2_table_init()

#==========================OAUTH2==========================
#Yishan@010272020 added for OAUTH2 modules
import oauth2.oauth2
global authorization
global OAUTH2_SESS
global OAUTH2_DbSESSION
global OAUTH2_ENGINE
global OAUTH2_DEREDIS
#在此建立oauth2所需的db_session
OAUTH2_SESS,OAUTH2_DbSESSION,OAUTH2_ENGINE,OAUTH2_DEREDIS,authorization = oauth2.oauth2.config_oauth(app)
print "********************"
print authorization

from oauth2.routes import OAUTH2_API
app.register_blueprint(OAUTH2_API)
import oauth2.routes
oauth2.routes.app = app
#=======================================================

#=======================================================
# Date: 12292020
# Yishan Tsai
# Only for IoT system to do this action
#=======================================================
if "IOT" in globalvar.SYSTEMLIST[globalvar.SERVERIP]:
    #create device init key
    iot_redis_device_keys_init("IOT", selfUse=True)
#=======================================================

#=======================================================
# Date: 03302021
# Yishan Tsai
# 初始設定apirecord的hash numer(apirecord_hash_num)
#=======================================================
from celeryApp.celeryTasks import apirecord_hash_num_init
apirecord_hash_num_init()
#=======================================================

#=======================================================
# API /dashboard
# Date: 10162020
# Yishan Tsai
# PaaS Dashboard
#=======================================================
# {{{ app.route('/dashboard', methods = ['GET'])
@app.route('/dashboard', methods=['GET'])
def paas_dashboard():
    # print "~~~~os.getpid()~~~~"
    # print os.getpid()
    # if(app.licCheck == 0) and (system in globalvar.SYSTEMLIST[globalvar.SERVERIP]):
    #     return render_template('index.html',api_map=api_map(system),system=system)
    # else:
    #     return "Not Found"
    # return render_template('index.html')
    return render_template("index.html")
    # return render_template('index.html',api_map=api_map(0,"SAPIDOSYSTEM"))
#}}}

#=======================================================
# API /dashboard
# Date: 10162020
# Yishan Tsai
# PaaS Dashboard
#=======================================================
# {{{ app.route('/dashboard', methods = ['GET'])
@app.route('/dashboard/ApiMap/<SYSTEM>', methods=['GET'])
def paas_dashboard_get_apimap(SYSTEM):
    if(app.licCheck == 0) and (SYSTEM in globalvar.SYSTEMLIST[globalvar.SERVERIP]):
        returndata = {"api_map":api_map(0,SYSTEM),"Response":"ok"}
    else:
        returndata = {"Response":"system:{} is illegal".format(SYSTEM)}
    return jsonify( **returndata)
#}}}

# #=======================================================
# # API /<system>/edoc
# # Date: 08162019
# # Yishan Tsai
# # 顯示所有api列表
# #=======================================================
# # {{{ app.route('/edoc', methods = ['GET'])
# @app.route('/apidocList', methods=['GET'])
# def get_apidoc_list(system):
#     if(app.licCheck == 0) and (system in globalvar.SYSTEMLIST[globalvar.SERVERIP]):
#         return render_template('index.html', api_map=api_map(system),system=system)
#     else:
#         return "Not Found"
# #}}}

#=======================================================
# API /edoc/<endpoint>
# Date: 08162019
# Yishan Tsai
# 返回apidoc
#=======================================================
# {{{ app.route('/edoc/<endpoint>', methods = ['GET'])
@app.route('/edoc/<endpoint>', methods=['GET'])
def docs(endpoint):
    # if(app.licCheck == 0) and (system in globalvar.SYSTEMLIST[globalvar.SERVERIP]):
    if(app.licCheck == 0):
        api = endpoint_api(endpoint)
        returndata = {"api":api,"Response":"ok"}
        return jsonify( **returndata)
    else:
        returndata = {"Response":"error"}
        return jsonify( **returndata)
#}}}

#=======================================================
# API /sandbox
# Date: 08162019
# Yishan Tsai
# 返回APIDOC sandbox環境測試api
#=======================================================
# {{{ app.route('/sandbox', methods = ['GET'])
@app.route('/sandbox', methods=['GET'])
def sandbox():
    # if(app.licCheck == 0) and (system in globalvar.SYSTEMLIST[globalvar.SERVERIP]):
    if(app.licCheck == 0):
        returndata = {"Response":"ok","api_map":api_map(2,"APIDOC")}
        return jsonify( **returndata)
    else:
        returndata = {"Response":"error"}
        return jsonify( **returndata)
#}}}

#{{{ edoc def
def api_map(level,SYSTEM=None):
    print "~~~~~SYSTEM~~~~~"
    print SYSTEM
    level_list = ["LEVEL1","LEVEL2","LEVEL3"]
    return [(x[0][1:], x[1], list(x[2].intersection(["GET", "POST", "PATCH", "PUT", "DELETE"]))[0], "/"+"/".join(x[0].split("/")[3:]), x[3]) for x in sorted(list(get_api_map(level_list[level],SYSTEM)))]

def endpoint_api(endpoint,onlydoc=False):
    api = {
        'endpoint': endpoint,
        'methods': [],
        'doc': '',
        'url': '',
        'name': ''
    }
    try:
        func = app.view_functions[endpoint]

        api['name'] = _get_api_name(func)
        api['doc'] = _get_api_doc(func)

        for rule in app.url_map.iter_rules():
            if rule.endpoint == endpoint:
                api['methods'] = list(set(rule.methods).intersection(["GET", "POST", "PATCH", "PUT", "DELETE"]))[0]
                api['url'] = "/".join(str(rule).split("/")[2:len(str(rule).split("/"))])
    except:
        api['doc'] = 'Invalid api endpoint: 「{}」!'.format(endpoint)

    if not onlydoc:
        return api
    if isinstance(api["doc"],dict):
        return api["doc"]["API_application"]
    # return api["doc"]
    return "api name: "+api['name']+" | api doc: "+api["doc"]

def _get_api_name(func):
    """e.g. Convert 'do_work' to 'Do Work'"""
    words = func.__name__.split('_')
    words = [w.capitalize() for w in words]
    return ' '.join(words)

def _get_api_doc(func):
    doc = func.__doc__
    if func.__doc__:
        if modules.VerifyDataStrLawyer(doc).verify_json():
            doc_obj = json.loads(doc)
            return doc_obj
        return 'Invalid , API doc is illegal JSON!'
    else:
        return 'Invalid , No doc found for this API!'

def get_api_map(level,SYSTEM):
    """Search API from rules, if match the pattern then we said it is API."""
    level_config = {
        #最高權限(但不包含APIDOC)
        "LEVEL1":r'/api/(?!APIDOC)',
        #只能看到通用系統api
        "LEVEL2":r'/api/<SYSTEM>',
        #只看得到APIDOC
        "LEVEL3":r'/api/APIDOC'
    }
    # regexStr = r'/api/{}/+'.format(system)
    for rule in app.url_map.iter_rules():
        if re.search(level_config[level],str(rule)):
            func = app.view_functions[rule.endpoint]
            if (isinstance(_get_api_doc(func),dict) and \
                ((_get_api_doc(func).get("ACCESS_SYSTEM_LIST") is None) or \
                (_get_api_doc(func).get("ACCESS_SYSTEM_LIST") is not None and SYSTEM in _get_api_doc(func)["ACCESS_SYSTEM_LIST"]))\
                ) or (isinstance(_get_api_doc(func),str)):
                yield str(rule), rule.endpoint, rule.methods, endpoint_api(rule.endpoint,onlydoc=True)
#}}}

#=======================================================
# API /dbDetail
# Date: 05252020
# Yishan Tsai
# 顯示api schema table
#=======================================================
# {{{ app.route('/<SYSTEM>/dbDetail', methods = ['GET'])
@app.route('/<SYSTEM>/dbDetail', methods = ['GET'])
def dbDetail(SYSTEM):
    dbDict = {"my":"MYSQL","ms":"MSSQL","myps":"POSTGRESQL"}
    dbDetail = {}
    regexStr = r'/Schema/'
    if SYSTEM in globalvar.SYSTEMLIST[globalvar.SERVERIP]:
        for x in sorted(list(get_api_map("LEVEL2",SYSTEM))):
            if re.search(regexStr,str(x[0])):
                dbDetail[dbDict[x[0].split("/")[4]]] = x[0]
            
        returndata = {"Response":"ok","dbDetail":dbDetail}
    else:
        returndata = {"Response":"system:{} is illegal".format(SYSTEM)}

    return jsonify( **returndata)
#}}}


def testthread(arg):
    #app.licCheck = os.system("/var/www/config/ezclient")
    while True:
        #app.licCheck = os.system("/var/www/config/ezclient")
        print '\n[{}]:Test Thread'.format(time.ctime())
        time.sleep(arg)


#initiate a thread to do the job, check Table:todolist's over deadline data every 24 hours
# thread.start_new_thread(testthread, (10, ))
# thread.start_new_thread(check_todolist_deadline, (60, ))


# print "%%%%%%%%%%%%%"
# def set_timer():
#     print "~~~~start set_timer~~~~~"
#     print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
#     time.sleep(30)
#     print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
#     thread.start_new_thread(check_todolist_deadline, (86400, ))
#     print "~~~~end set_timer~~~~~"
#     print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]

# thread.start_new_thread(set_timer, ())
print "8"
if __name__ == "__main__":
    print "9"
    #isDebug=0
    #if len(sys.argv) == 2:
    #    if sys.argv[1] == "debug":
    #        isDebug = 1

    #from werkzeug.contrib.profiler import ProfilerMiddleware
    #app.wsgi_app = ProfilerMiddleware(app.wsgi_app)
    # app.run(host='0.0.0.0',port=65000,debug=isDebug)

    #Yishan 09092020 app.run -> socketio.run
    socketio.run(app,port=3687,debug=True)


