# coding=utf-8 
#Description
"""
==============================================================================
created    : 10/01/2020

Last update: 02/08/2021

Developer: Yishan Tsai 

Lite Version 1 @Yishan05212020

Filename: socketHandle.py

Description: sockec modules
==============================================================================
"""
from sqlalchemy import *
from flask_socketio import send, emit, join_room, leave_room, close_room, rooms, disconnect

from app import *
#Yishan@05212020 added for common modules
from app.modules import *

import thread

_keep = False
_monitorLogging = {}

class GetImmediateLoggingFile():
    def __init__(self):
        self.switch = False
        self.filename = ""
        self.system = ""
        self.init = True
        self.is_uWSGI = True
    
    def work(self,dbsess={}):
        # print "&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&"
        if self.filename == "" or self.filename is None:
            self.switch = False
        else:
            this_socketio_name = "watch_"+self.filename.split("-")[0]
            #print "~~~~~this_socketio_name~~~~~"
            #print this_socketio_name
            #print '"globalvar.LOGFILELIST[self.filename]["path"]" -> {}'.format(globalvar.LOGFILELIST[self.filename.split("-")[0]]["path"])
            #print '"globalvar.LOGFILELIST[self.filename]["filename"]" -> {}'.format(globalvar.LOGFILELIST[self.filename.split("-")[0]]["filename"])
            thisfilepath = globalvar.LOGFILELIST[self.filename.split("-")[0]]["path"]+globalvar.LOGFILELIST[self.filename.split("-")[0]]["filename"]
            if self.filename != "uWSGI_LOG":
                thisfilepath = globalvar.LOGFILELIST[self.filename.split("-")[0]]["path"]+self.system+"/"+globalvar.LOGFILELIST[self.filename.split("-")[0]]["filename"]
            #print '"thisfilepath" -> {}'.format(thisfilepath)

        global _monitorLogging
        # print "=====self.switch====="
        #print self
        # print self.switch
        while self.switch:
            socketio.sleep(2)
            # print "{} | {}".format(os.getpid(),datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::])
            if self.is_uWSGI:
                with open(thisfilepath, "r") as readfile:
                    lines = readfile.readlines()
                    if len(lines) > 20 and _monitorLogging["already_print_num"+self.filename] == 0:
                        #last_num = 20  #????????????????????????20???
                        #???nonoob?????????????????????
                        _monitorLogging["already_print_num"+self.filename] = len(lines) - 20

                    if _monitorLogging["already_print_num"+self.filename] < len(lines):
                        print_lines = lines[_monitorLogging["already_print_num"+self.filename] - len(lines):]
                        for i in range(len(print_lines)):
                            returnnum = _monitorLogging["already_print_num"+self.filename]+i+1
                            socketio.emit(this_socketio_name,
                                        {'data': print_lines[i].replace('\n',''),"already_print_num":returnnum},
                                        room=self.filename)
                                        # broadcast=True)
                        _monitorLogging["already_print_num"+self.filename] = len(lines)
            else:
                logTable = Table(self.system.lower()+"_paas_log_"+time.strftime("%Y%m", time.localtime()), dbsess["meta"],  autoload=True)

                totalData = []
                for row in dbsess["sess"].query(logTable):
                    drow = AdjustDataFormat().format(row._asdict())
                    totalData.append(drow)

                datacounts = len(totalData)

                if datacounts > 20 and _monitorLogging["already_print_num"+self.filename] == 0:
                    #last_num = 20  #????????????????????????20???
                    #???nonoob?????????????????????
                    _monitorLogging["already_print_num"+self.filename] = datacounts - 20
                
                if _monitorLogging["already_print_num"+self.filename] < datacounts:
                    print_lines = totalData[_monitorLogging["already_print_num"+self.filename] - datacounts:]
                    for i in range(len(print_lines)):
                        thisdata = AdjustDataFormat().format(print_lines[i])
                        returnnum = _monitorLogging["already_print_num"+self.filename]+i+1
                        socketio.emit(this_socketio_name,
                                    {'data': thisdata,"already_print_num":returnnum},
                                    room=self.filename)
                                    # broadcast=True)
                    _monitorLogging["already_print_num"+self.filename] = datacounts
    
    def start(self,filename,system,is_uWSGI):
        print "======start====="
        self.switch = True
        self.filename = filename
        self.system = system
        self.is_uWSGI = is_uWSGI
        # print "================"
        # print self.switch
        # print self.filename
        # print self.system
        print self.init
        # print self.is_uWSGI
        # print "================"
        if self.init:
            self.init = False
            #print '"self.init" -> {}'.format(self.init)
            if self.is_uWSGI:
                self.work()
            else:
                try:
                    DbSessionRaw,metaRaw,engineRaw = appPaaS.getDbSessionType(system="PaaS",dbName="paas_dashboard",forRawData="postgres")
                    if DbSessionRaw is not None:
                        sessRaw = DbSessionRaw()
                        self.work({"sess":sessRaw,"meta":metaRaw})
                except Exception as e:
                    err_msg = appPaaS.catch_exception(e,sys.exc_info(),"PaaS")
                finally:
                    if DbSessionRaw is not None:
                        print "@@@@@@@@@@@@@"
                        sessRaw.close()
                        DbSessionRaw.remove()
                        engineRaw.dispose()
                        print "@@@@@@@@@@@@@"

            #print "======end work()====="

    def stop(self):
        print "======stop====="
        #print self
        self.switch = False
        # print "======reset_init====="
        self.init = True
    
    def check_status(self):
        return self.switch

#socket?????????????????????
@socketio.on('connect')
def connect():
    global _monitorLogging
    # print "~~~~os.getpid()~~~~"
    # print os.getpid()
    # print "~~~~_monitorLogging~~~~"
    # print _monitorLogging
    _monitorLogging[request.sid] = None
    # print '"_monitorLogging.get("filethreadlist")" -> {}'.format(_monitorLogging.get("filethreadlist"))
    if _monitorLogging.get("filethreadlist") is None:
        # print "!!!!!!!!!!here!!!!!!!!!!!!!!!"
        _monitorLogging["filethreadlist"] = {}
        _monitorLogging["eachroom_peoplenum"] = {}
        _monitorLogging["loggingclass"] = {}

        c = []
        import copy
        temp_systemList = copy.deepcopy(globalvar.SYSTEMLIST[globalvar.SERVERIP])
        temp_systemList.remove("APIDOC")
        for i in globalvar.LOGFILELIST.keys():
            if i == "PaaS_LOG":
                c = c+list(map(lambda x,y: x+"-"+y,[i]*len(temp_systemList),temp_systemList))
                #https://yangfangs.github.io/2017/08/23/python-map-zip-filter-reduce/
            else:
                c.append(i)
        else:
            for i in c:
                _monitorLogging["filethreadlist"]["thread_"+i] = None
                _monitorLogging["already_print_num"+i] = 0
                _monitorLogging["eachroom_peoplenum"][i] = []

    with lock:
        for key,value in _monitorLogging["filethreadlist"].items():
            # print "~~~~key,value~~~~"
            # print key,value
            if value is None:
                _monitorLogging["loggingclass"][key] = GetImmediateLoggingFile()
                # print '"_monitorLogging["loggingclass"][key]" -> {}'.format(_monitorLogging["loggingclass"][key]) 
                _monitorLogging["filethreadlist"][key] = socketio.start_background_task(target=_monitorLogging["loggingclass"][key].work)
            # else:
            #     print '"_monitorLogging["loggingclass"][key]" -> {}'.format(_monitorLogging["loggingclass"][key]) 
    
    # print "~~~~request.sid~~~~"
    # print request.sid
    emit("re_connect", {"msg": "connected","sid":request.sid})
    print ".....connect....."

@socketio.on('disconnect')  
def disconnect():
    global _keep
    _keep = False

    global _monitorLogging
    #?????????disconnect???request.sid???????????????????????????????????????????????????????????????
    if _monitorLogging[request.sid] is not None:
        _monitorLogging["eachroom_peoplenum"][_monitorLogging[request.sid]].remove(request.sid)
        #????????????????????????????????????request.sid????????????0???????????????????????????????????????????????????class read log file
        if len(_monitorLogging["eachroom_peoplenum"][_monitorLogging[request.sid]]) == 0:
            #??????room
            close_room(_monitorLogging[request.sid])
            _monitorLogging["loggingclass"]["thread_"+_monitorLogging[request.sid]].stop()
            
    #?????????global??????request.sid
    del _monitorLogging[request.sid]

    #????????????-?????????????????????
    thread.start_new_thread(timer,())
    print ".....disconnect....."

@socketio.on('monitor')
def get_file(data):
    # print "==================get_file=================="
    # print "~~~~data~~~~"
    # print data
    is_uWSGI = True
    filename = data["filename"]
    if data["filename"] == "PaaS_LOG":
        filename = data["filename"]+"-"+data["system"]
        is_uWSGI = False
    # print "~~~~filename~~~~"
    # print filename
    # print "~~~is_uWSGI~~~~"
    # print is_uWSGI

    join_room(filename)

    global _monitorLogging
    # print "~~~~_monitorLogging[request.sid]~~~~~"
    # print _monitorLogging[request.sid]
    # print "~~~~_monitorLogging[eachroom_peoplenum]~~~~~"
    # print _monitorLogging["eachroom_peoplenum"]

    #????????????request.sid??????????????????room(????????????room????????????)
    if _monitorLogging[request.sid] is not None:
        if _monitorLogging[request.sid] != filename:
            leave_room(_monitorLogging[request.sid])
            #????????????????????????request.sid
            _monitorLogging["eachroom_peoplenum"][_monitorLogging[request.sid]].remove(request.sid)
            # print "~~~~_monitorLogging[eachroom_peoplenum][_monitorLogging[request.sid]]~~~~~"
            # print _monitorLogging["eachroom_peoplenum"][_monitorLogging[request.sid]]
            #????????????????????????????????????request.sid????????????0???????????????????????????????????????????????????class read log file
            if len(_monitorLogging["eachroom_peoplenum"][_monitorLogging[request.sid]]) == 0:
                #??????room
                close_room(_monitorLogging[request.sid])
                _monitorLogging["loggingclass"]["thread_"+_monitorLogging[request.sid]].stop()
        

    #????????????request.sid????????????????????????
    _monitorLogging[request.sid] = filename
    #print "~~~~_monitorLogging[request.sid]~~~~~"
    #print _monitorLogging[request.sid]

    #???request.sid??????????????????????????????
    if request.sid not in _monitorLogging["eachroom_peoplenum"][filename]:
        _monitorLogging["eachroom_peoplenum"][filename].append(request.sid)

    #print '~~~~_monitorLogging["already_print_num"+filename]~~~~~'
    #print _monitorLogging["already_print_num"+filename]
    #???already_print_num???0
    _monitorLogging["already_print_num"+filename] = 0

    #print '~~~~_monitorLogging["loggingclass"]["thread_"+filename]~~~~'
    #print _monitorLogging["loggingclass"]["thread_"+filename]
    # print '~~~~_monitorLogging["loggingclass"]["thread_"+filename].check_status()~~~~'
    # print _monitorLogging["loggingclass"]["thread_"+filename].check_status()
    # print "~~~~os.getpid()~~~~"
    # print os.getpid()

    if not _monitorLogging["loggingclass"]["thread_"+filename].check_status():
        _monitorLogging["loggingclass"]["thread_"+filename].start(filename,data["system"],is_uWSGI)

@socketio.on('keep')
def keep():
    global _keep
    _keep = True 

def timer():
    # print("-------------------------------------------")
    # print(datetime.now().strftime('%Y-%m-%d %H:%M:%S'))
    time.sleep(10) #60????????????????????????socket connect
    # print(datetime.now().strftime('%Y-%m-%d %H:%M:%S'))
    # print("-------------------------------------------")
    # print(keep)
    if not _keep:
        global _monitorLogging
        for key,value in _monitorLogging["filethreadlist"].items():
            _monitorLogging["loggingclass"][key].stop()