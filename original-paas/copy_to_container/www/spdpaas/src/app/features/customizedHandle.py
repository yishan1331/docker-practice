# -*- coding: utf-8 -*-
#Department Module Description
"""
==============================================================================
created    : 03/20/2017
Last update: 02/08/2021
Developer: Wei-Chun Chang 
Lite Version 2 @Yishan08212019
API version 1.0
 
Filename: customizedHandle.py

Description: basically, all writes to the module will be opened to superuser only, for others, can only query data
    1. register a department
    2. query Department basic info.
    3. query Department members
    4. query Department sensors
Total = 6 APIs
==============================================================================
"""

#=======================================================
# System level modules
#=======================================================
#{{{
from sqlalchemy import *
from werkzeug.security import gen_salt
import subprocess #Yishan 05212020 subprocess 取代 os.popen
# import threading
#}}}

#=======================================================
# User level modules
#=======================================================
#{{{
from app import *
#Yishan@05212020 added for common modules
from app.modules import *
#}}}

__all__ = ('trigger_specific_program','iot_redis_device_keys_init')

ACCESS_SYSTEM_LIST = ["IOT"]

# # 建立 thread lock
# lock = threading.Lock()

#blueprint
CUSTOMIZED_API = Blueprint('CUSTOMIZED_API', __name__)

#{{{ def _list_iter(name)
def _list_iter(r,name):
    """
    自定义redis列表增量迭代
    :param name: redis中的name，即：迭代name对应的列表
    :return: yield 返回 列表元素
    """
    list_count = r.llen(name)
    for index in range(list_count):
        yield r.lindex(name, index)
#}}}

class TriggerProgramWorkerThread(threading.Thread):
    def __init__(self, pid, lock, func, cmd, SYSTEM):
        threading.Thread.__init__(self)
        self.pid = pid
        self.func = func
        self.lock = lock
        self.cmd = cmd
        self.SYSTEM = SYSTEM

    def run(self):
        # 取得 lock
        # self.lock.acquire()
        # print "@@@@@@@@@@@@@@@@@@@@@@@TriggerProgram@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@"
        # print "------Lock-----",self.pid
        # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::],self.pid
        # print "Lock acquired by pid %d" % self.pid
        # print "------threading detail------",os.getpid()
        # print threading.active_count() #用來查看目前有多少個線程
        # print threading.current_thread().name #可以用來查看你在哪一個執行緒當中

        self.func(self.cmd,self.SYSTEM)

        # 釋放 lock
        # self.lock.release()
        # print "------released Lock-----",self.pid
        # print "Lock released by pid %d" % self.pid
        # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::],self.pid
        # print "------threading detail------",os.getpid()
        # print threading.active_count() #用來查看目前有多少個線程
        # print threading.current_thread().name #可以用來查看你在哪一個執行緒當中
        # print "@@@@@@@@@@@@@@@@@@@@@@@TriggerProgram end@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@"
        # print 

#=======================================================
# subprocess_check_output_program
# Date: 12142020@Yishan
# https://www.coder.work/article/3210794
# https://stackoverflow.com/questions/31683320/suppress-stderr-within-subprocess-check-output
#=======================================================
# {{{ def subprocess_check_output_program(cmd)
def subprocess_check_output_program(cmd,SYSTEM):
    try:
        # print "------func------",os.getpid()
        # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
        # print cmd
        # process = subprocess.check_output(cmd,shell=False,stderr=subprocess.STDOUT)  
        with open(os.devnull, 'w') as devnull:
            process = subprocess.check_output(cmd,shell=False,stderr=devnull)
            # with subprocess.Popen(cmd, shell=False, stdout=subprocess.PIPE, stderr=subprocess.PIPE) as proc:
            # process = subprocess.Popen(cmd, shell=False, stdout=devnull, stderr=devnull)
            # result = process.stdout.readlines()
            # print "process.pid-> ",process.pid
            # print process
            # print "result-> ",result
        # print os.getpid(),"~~~~subprocess_check_output_program~~~~",process
        # print "------------------"
        # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
        # print "======================="
        return True,process,{"returncode":0}
    except subprocess.CalledProcessError as e:
        print "~~~~e~~~~"
        print e.__dict__
        return False,appPaaS.catch_exception(e,sys.exc_info(),SYSTEM),e.__dict__
# }}}

#=======================================================
# 列出/var/www/html/download/files內所有檔案
# Date: 12142020@Yishan
#=======================================================
# {{{ CUSTOMIZED_API.route('/api/PaaS/1.0/Customized/Show/DownloadFiles', methods = ['POST']),
@CUSTOMIZED_API.route('/api/PaaS/1.0/Customized/Show/DownloadFiles', methods = ['GET'])
def show_downloadfiles(selfUse=False):
    err_msg = "ok"
    FILEPATH = "/var/www/html/download/files"

    if not selfUse:
        dicRet = appPaaS.preProcessRequest(request,system="PaaS")

        uri_parameter = ["uid"]
        result, result_msg = check_uri_parameter_exist(request,uri_parameter)
        if not result:
            dicRet["Response"] = result_msg
            return jsonify( **dicRet)

    fileList = []
    try:
        fileList = [f for f in os.listdir(FILEPATH)]

    except Exception as e:
        err_msg = appPaaS.catch_exception(e,sys.exc_info(),"PaaS")

    if not selfUse:
        dicRet["FileList"] = fileList
        dicRet["Response"] = err_msg
        return jsonify( **dicRet)

    return fileList
# }}}

#=======================================================
# 提供使用者生成下載檔案列表之id & pwd (gen_salt)
# Date: 12142020@Yishan
#=======================================================
# {{{ CUSTOMIZED_API.route('/api/PaaS/1.0/Customized/Create/DownloadFiles/IdPwd', methods = ['POST']),
@CUSTOMIZED_API.route('/api/PaaS/1.0/Customized/Create/DownloadFiles/IdPwd', methods = ['POST'])
@decorator_check_content_type(request)
def create_downloadfiles_idpwd():
    err_msg = "ok"
    dicRet = appPaaS.preProcessRequest(request,system="PaaS")

    uri_parameter = ["uid"]
    result, result_msg = check_uri_parameter_exist(request,uri_parameter)
    if not result:
        dicRet["Response"] = result_msg
        return jsonify( **dicRet)

    if not VerifyDataStrLawyer(request.data).verify_json():
        dicRet["Response"] = "error input '{}' is illegal JSON".format(request.data)
        return jsonify( **dicRet)

    reqdataList = ConvertData().convert(json.loads(request.data))
    if not isinstance(reqdataList,list):
        dicRet["Response"] = "post data必須傳陣列"
        return jsonify( **dicRet)

    FILEPATH = "/var/www/html/download/files/"

    #檢查丟上來的data是否存在
    for i in reqdataList:
        if not isinstance(i,str):
            dicRet["Response"] = "'{}' 必須為字串".format(i)
            return jsonify( **dicRet)

        if not os.path.isfile(os.path.join(FILEPATH,i)):
            dicRet["Response"] = "{} 檔案不存在或路徑有誤".format(i)
            return jsonify( **dicRet)

    ID = gen_salt(24)
    PWD = gen_salt(48)

    recList = []
    try:
        dbRedis,_,result= appPaaS.getDbSessionType(system="PaaS",dbName=15,forRawData="redis")
        if dbRedis is None:
            #表示連接資料庫有問題
            dicRet["Response"] = result
            return jsonify( **dicRet)

        redis_key = ID+"_"+PWD

        #設立基準點過期時間為後天的00:30
        rederence_extime = int(time.mktime(time.strptime(str(date.today() + timedelta(days=2))+" 00:30:00", '%Y-%m-%d %H:%M:%S')))
        redishash_already_set_expireat = False

        #基準點若存在需判斷此次建立是否需增加基準點期限秒數(小於兩天：172800s，直接以今天為基準多加兩天)
        if dbRedis.exists("rederence_point"):
            if dbRedis.ttl("rederence_point") < 172800:
                dbRedis.expireat("rederence_point",rederence_extime)
                redishash_already_set_expireat = True
            # if dbRedis.hexists("rederence_point",redis_key): #正常情況下不可能會有重複ID&PWD，但若重複了，while重新建立一次
            #     status = True
            #     while status:
            #         redis_key = str(gen_salt(24))+"_"+str(gen_salt(48))
            #         status = dbRedis.hexists("rederence_point",redis_key)
            # else:
            #     dbRedis.hmset("rederence_point",{redis_key:json.dumps(reqdataList)})
        #不存在，建立基準點value為檔案列表，期限為兩天後 (hash)
        # else:
        #     dbRedis.hmset("rederence_point",{redis_key:json.dumps(reqdataList)})
        #     dbRedis.expireat("rederence_point",rederence_extime)

        # rederence_point : {"filename":[redis_key,.....]}

        #建立ID_PWD:reqdataList(list)
        if dbRedis.llen(redis_key) != 0:
            status = True
            while status:
                redis_key = str(gen_salt(24))+"_"+str(gen_salt(48))
                if dbRedis.llen(redis_key) == 0: status = False

        for i in reqdataList:
            dbRedis.lpush(redis_key, i)
            if dbRedis.hexists("rederence_point",i):
                this_list = json.loads(dbRedis.hget("rederence_point", i))
                this_list.append(redis_key)
                dbRedis.hmset("rederence_point",{i:json.dumps(this_list)})
            else:
                dbRedis.hmset("rederence_point",{i:json.dumps([redis_key])})
        dbRedis.expire(redis_key,86400)
        if not redishash_already_set_expireat:
            dbRedis.expireat("rederence_point",rederence_extime)

        dicRet["ID"] = ID
        dicRet["PWD"] = PWD
        dicRet["DownloadList"] = reqdataList
        err_msg = "ok"

    except Exception as e:
        err_msg = appPaaS.catch_exception(e,sys.exc_info(),"PaaS")

    dicRet["Response"] = err_msg
    return jsonify( **dicRet)
#}}}

#=======================================================
# 檢驗欲使用下載檔案功能之id & pwd合法性
# Date: 12142020@Yishan
#=======================================================
# {{{ CUSTOMIZED_API.route('/api/PaaS/1.0/Customized/Check/DownloadFiles/IdPwd', methods = ['POST']),
@CUSTOMIZED_API.route('/api/PaaS/1.0/Customized/Check/DownloadFiles/IdPwd', methods = ['POST'])
@decorator_check_content_type(request)
def check_downloadfiles_idpwd():
    err_msg = "ok"
    dicRet = appPaaS.preProcessRequest(request,system="PaaS")

    uri_parameter = ["uid"]
    result, result_msg = check_uri_parameter_exist(request,uri_parameter)
    if not result:
        dicRet["Response"] = result_msg
        return jsonify( **dicRet)

    if not VerifyDataStrLawyer(request.data).verify_json():
        dicRet["Response"] = "error input '{}' is illegal JSON".format(request.data)
        return jsonify( **dicRet)
    
    reqdataDict = ConvertData().convert(json.loads(request.data))

    post_parameter = ["Id","Pwd"]
    if not check_post_parameter_exist(reqdataDict,post_parameter):
        dicRet["Response"] = "Missing post parameters : '{}'".format(post_parameter)
        return jsonify( **dicRet)
    
    Id = reqdataDict.get("Id").encode("utf8").strip()
    Pwd = reqdataDict.get("Pwd").encode("utf8").strip()

    recList = []
    try:
        dbRedis,_,result= appPaaS.getDbSessionType(system="PaaS",dbName=15,forRawData="redis")
        if dbRedis is None:
            #表示連接資料庫有問題
            dicRet["Response"] = result
            return jsonify( **dicRet)

        redis_key = Id+"_"+Pwd

        if not dbRedis.exists(redis_key):
            dicRet["Response"] = "帳號或密碼錯誤"
            return jsonify( **dicRet)
        
        download_List = []
        download_List = [item for item in _list_iter(dbRedis,redis_key)]

        dicRet["DownloadList"] = download_List
        err_msg = "ok"

    except Exception as e:
        err_msg = appPaaS.catch_exception(e,sys.exc_info(),"PaaS")

    dicRet["Response"] = err_msg
    return jsonify( **dicRet)
#}}}

#=======================================================
# 檢驗欲使用下載檔案的有效期限若超過則刪除
# Date: 12142020@Yishan
#=======================================================
# {{{ CUSTOMIZED_API.route('/api/PaaS/1.0/Customized/Check/Delete/DownloadFiles/Deadline/<CRONTAB>', methods = ['GET']),
@CUSTOMIZED_API.route('/api/PaaS/1.0/Customized/Check/Delete/DownloadFiles/Deadline/<CRONTAB>', methods = ['GET'])
def check_delete_downloadfiles_deadline(CRONTAB="DAY"):
    err_msg = "ok"
    dicRet = appPaaS.preProcessRequest(request,system="PaaS")

    uri_parameter = ["uid"]
    result, result_msg = check_uri_parameter_exist(request,uri_parameter)
    if not result:
        dicRet["Response"] = result_msg
        return jsonify( **dicRet)

    FILEPATH = "/var/www/html/download/files/"

    try:
        dbRedis,_,result= appPaaS.getDbSessionType(system="PaaS",dbName=15,forRawData="redis")
        if dbRedis is None:
            #表示連接資料庫有問題
            dicRet["Response"] = result
            return jsonify( **dicRet)

        #此為每天的crontab要做的排程(00:45)
        #抓出redis hash : rederence_point內的檔案(key):[id&pwd(value)]，一一查看value是否exists in redis，不存在代表id&pwd已過期，即可移除此value；
        #when loop run over,len=0 => 此檔案無人要下載了，直接刪除 , else update new value
        if CRONTAB == "DAY":
            for key,value in dbRedis.hgetall("rederence_point").items():
                new_value = []
                for i in json.loads(value):
                    if dbRedis.exists(i):
                        new_value.append(i)
                else:
                    #新的value list沒有資料，將此檔案刪除，rederence_point hash key del
                    if not new_value:
                        if os.path.isfile(os.path.join(FILEPATH,key)): os.remove(os.path.join(FILEPATH,key))
                        dbRedis.hdel("rederence_point", key)
                    else:
                        dbRedis.hmset("rederence_point",{key:json.dumps(new_value)})
        #此為每個禮拜天的crontab要做的排程(Sunday 01:00)
        #先抓出所有檔案，再去redis看這些檔案(key)在hash : rederence_point是否存在，存在代表有id&pwd需要下載，不存在表示無人下載即可刪除
        elif CRONTAB == "WEEK":
            fileList = show_downloadfiles(True)
            for i in fileList:
                if i not in dbRedis.hkeys("rederence_point") and os.path.isfile(os.path.join(FILEPATH,i)): os.remove(os.path.join(FILEPATH,i))

        err_msg = "ok"

    except Exception as e:
        err_msg = appPaaS.catch_exception(e,sys.exc_info(),"PaaS")

    dicRet["Response"] = err_msg
    return jsonify( **dicRet)
#}}}

#=======================================================
# 提供api觸發指定程式
# Date: 12142020@Yishan
#=======================================================
# {{{ CUSTOMIZED_API.route('/api/<SYSTEM>/1.0/Customized/Trigger/Specific/Program', methods = ['GET']),
@CUSTOMIZED_API.route('/api/<SYSTEM>/1.0/Customized/Trigger/Specific/Program', methods = ['POST'])
@docstring_parameter(ACCESS_SYSTEM_LIST=ACCESS_SYSTEM_LIST)
def trigger_specific_program(SYSTEM,selfUse=False,useThread=False,languages=None,programName=None,programData=None,Temp=False):
    #{{{APIINFO
    '''
    {
        "API_application":"提供觸發指定程式",
        "API_parameters":{"uid":"使用者帳號"},
        "API_path_parameters":{"SYSTEM":"合法的系統名稱"},
        "API_postData":{
            "bodytype":"Object",
            "bodyschema":"{}",
            "parameters":{
                "languages":{"type":"String","requirement":"required","directions":"欲觸發的程式語言類型","example":"php"},
                "programName":{"type":"String","requirement":"required","directions":"欲觸發的程式路徑加檔名","example":"/var/www/html/test.php"},
                "programData":{"type":"Unlimited","requirement":"optional","directions":"欲丟入觸發程式的參數資料","example":"test"}
            },
            "precautions":{
                "注意事項1":"languages目前只接受php語言",
                "注意事項2":"programName程式路徑必須存在"
            },
            "example":[
                {
                    "languages":"php",
                    "programName":"test.php",
                    "programData":"123"
                }
            ]
        },
        "API_message_parameters":{"GetProgramResponse":"Unlimited+取得觸發程式回傳的值"},
        "API_example":{
            "Response": "ok",
            "APIS": "POST /api/IOT/1.0/Customized/Trigger/Specific/Program",
            "OperationTime": "3.020",
            "BytesTransferred": 223,
            "System": "IOT",
            "GetProgramResponse": "test"
        }
    }
    '''
    #}}}
    err_msg = "error"

    languages_config = {
        "php":"/usr/bin/php",
        "c":""
    }
    if not selfUse:
        dicRet = appPaaS.preProcessRequest(request,system=SYSTEM)
        # if SYSTEM not in list(set(globalvar.SYSTEMLIST[globalvar.SERVERIP]).intersection(set(ACCESS_SYSTEM_LIST))):
        #     dicRet["Response"] = "system:{} has no privillege to use this API".format(SYSTEM)
        #     return jsonify( **dicRet)
        
        uri_parameter = ["uid"]
        result, result_msg = check_uri_parameter_exist(request,uri_parameter)
        if not result:
            dicRet["Response"] = result_msg
            return jsonify( **dicRet)

        if not VerifyDataStrLawyer(request.data).verify_json():
            dicRet["Response"] = "error input '{}' is illegal JSON".format(request.data)
            return jsonify( **dicRet)

        reqdataDict = json.loads(request.data)
        if isinstance(reqdataDict,type(u"")):
            reqdataDict = json.loads(reqdataDict)

        post_parameter = ["languages","programName","programData"]
        if not check_post_parameter_exist(reqdataDict,post_parameter):
            dicRet["Response"] = "Missing post parameters : '{}'".format(post_parameter)
            return jsonify( **dicRet)
    
        languages = reqdataDict.get("languages")
        programName = reqdataDict.get("programName")
        programData = reqdataDict.get("programData")
    
        # print "~~~~languages~~~~"
        # print languages
        if languages not in languages_config.keys():
            dicRet["Response"] = "Currently only php and C programs can be executed"
            return jsonify( **dicRet)
        
        # print "~~~~programName~~~~"
        # print programName
        # print "~~~~programData~~~~"
        # print programData
        # print type(programData)
        if isinstance(programData,dict): programData = json.dumps(programData)
        # print "~~~~programData~~~~"
        # print programData

        if not os.path.isfile(programName):
            dicRet["Response"] = "{} 檔案不存在或路徑有誤".format(programName)
            return jsonify( **dicRet)

    cmd = [languages_config[languages],programName]
    if programData: cmd.append(programData)
    # cmd = "{}{}".format(languages_config[languages],programName)
    # if programData: cmd+=" '{}'".format(programData)
    # print "~~~cmd~~~"
    # print cmd
    try:
        if useThread:
            # print "~~~~~trigger start~~~~~~"
            # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]

            if Temp:
                from celeryApp.celeryTasks import celery_trigger_specific_program
                celery_trigger_specific_program.apply_async(args=(cmd,SYSTEM), routing_key='high', queue="H-queue1")
            else:
                worker = TriggerProgramWorkerThread(os.getpid(), lock, subprocess_check_output_program, cmd, SYSTEM)
                worker.start()

            # print "~~~~~trigger over~~~~~~"
            # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
            err_msg = "ok"
            return
        else:
            if languages == "c": cmd.pop(0)
            # print "!!!!!!!!!!!!!!!!!"
            dicRet["StartProgramTime"] = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            process = subprocess_check_output_program(ConvertData().convert(cmd),SYSTEM)
            # print "~~~~process~~~~"
            # print process
            # print "~~~~type process~~~~"
            # print type(process)
            dicRet["EndProgramTime"] = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            # print "!!!!!!!!!!!!!!!!!"
            if process[0]:
                dicRet["GetProgramResponse"] = {"output":process[1],"returncode":0}
                err_msg = "ok"
            else:
                # print process[2]
                del process[2]["cmd"]
                dicRet["GetProgramResponse"] = process[2]
                err_msg = "error"

    except Exception as e:
        print "~~~Exception~~~"
        print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
        print e
        print sys.exc_info()
    finally:
        if not selfUse:
            # dicRet["THISTIME"] = datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
            dicRet["Response"] = err_msg
            return jsonify(**dicRet)
#}}}

#=======================================================
# Definition: For IoT 初始化IoT所需的redis mes_device_status keys(hash) from mysql table:
# Date: 12292020@Yishan
#=======================================================
# {{{def iot_redis_device_keys_init(SYSTEM)
@CUSTOMIZED_API.route('/api/<SYSTEM>/1.0/Customized/Init/Redis/Device/Keys', methods = ['GET'])
@docstring_parameter(ACCESS_SYSTEM_LIST=ACCESS_SYSTEM_LIST)
def iot_redis_device_keys_init(SYSTEM, selfUse=False):
    """
    For IoT 初始化IoT所需的redis device keys(hash)

    Args:
        SYSTEM: 使用之系統名稱
    Returns:
        no return
    """
    if not selfUse:
        dicRet = appPaaS.preProcessRequest(request,system=SYSTEM)
        
        uri_parameter = ["uid"]
        result, result_msg = check_uri_parameter_exist(request,uri_parameter)
        if not result:
            dicRet["Response"] = result_msg
            return jsonify( **dicRet)

    all_device = {}
    err_msg = "error"
    try:
        DbSession,metadata,engine= appPaaS.getDbSessionType(system=SYSTEM)
        if DbSession is None:
            return

        sess = DbSession()
        
        queryTable = Table("preload" , metadata, autoload=True)

        for row in sess.query(queryTable).all():
            drow = AdjustDataFormat().format(row._asdict())
            all_device[drow["main_key"]+"_"+drow["combine_key"]] = json.loads(drow["combine_list"])
        
        err_msg = "ok" #done successfully
        # http://stackoverflow.com/questions/4112337/regular-expressions-in-sqlalchemy-queries

    except Exception as e:
        err_msg = appPaaS.catch_exception(e,sys.exc_info(),SYSTEM)

    finally:
        if 'DbSession' in locals().keys() and DbSession is not None:
            sess.close()
            DbSession.remove()
            engine.dispose()
    
        if err_msg != "ok":
            if selfUse: return 
            if not selfUse:
                dicRet["Response"] = err_msg
                return jsonify( **dicRet)

    err_msg = "error"
    try:
        redis_db = globalvar.SYSTEMLIST[globalvar.SERVERIP].index(SYSTEM)
        dbRedis,_,_ = appPaaS.getDbSessionType(system=SYSTEM,dbName=redis_db,forRawData="redis")
        if dbRedis is None:
            return

        for key,value in all_device.items():
            #若key不存在，直接建立
            if not dbRedis.exists(key):
                dbRedis.hmset(key, value)
            #若存在，比較value物件的key，抓取不重複的建立
            else:
                #差集(舊的多的key，需刪除)
                fields_need_del = list(set(dbRedis.hkeys(key)).difference(value.keys()))
                if fields_need_del: dbRedis.hdel(key, *fields_need_del)
                #差集(新的多的key，需新增)
                fields_need_add = list(set(value.keys()).difference(dbRedis.hkeys(key)))
                if fields_need_add:
                    for value_key,value_value in value.items():
                        if value_key in fields_need_add:
                            dbRedis.hset(key, value_key, value_value)
        
        #檢查mes_device_status_* keys是否需刪除(多的需刪除)
        keys_need_del = list(set(dbRedis.keys("mes_device_status_*")).difference(all_device.keys()))
        if keys_need_del: dbRedis.delete(*keys_need_del)
        err_msg = "ok"

    except Exception as e:
        err_msg = appPaaS.catch_exception(e, sys.exc_info(), SYSTEM)
    
    finally:
        if not selfUse:
            dicRet["Response"] = err_msg
            return jsonify( **dicRet)
#}}}