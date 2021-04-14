# coding=utf-8 
#Description
"""
==============================================================================
created    : 10/27/2020

Last update: 03/31/2021

Developer: Yishan Tsai 

Lite Version 1 @Yishan05212020

Filename: emailHandle.py

Description: email modules

Total = 2 APIs
==============================================================================
"""
import sys
#wei@02262019
reload(sys)
sys.setdefaultencoding('utf-8')
import json
from datetime import datetime

from flask import request, jsonify, Blueprint

from sqlalchemy import Table, and_
import smtplib
from email.mime.text import MIMEText
from email.header import Header
from email.utils import formataddr,parseaddr

from apiPortal import app
import modules

__all__ = ('EMAIL_API', 'check_todolist_deadline')

#blueprint
EMAIL_API = Blueprint('EMAIL_API', __name__)

@EMAIL_API.route('/api/PaaS/1.0/email/sendEmails', methods=['POST'])
def sending_email(emailData=[],selfUse=False):
    # print "============in request================"
    # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
    err_msg = ""
    if not selfUse:
        dicRet = app.preProcessRequest(request,system="PaaS")

        uri_parameter = ["uid"]
        result, result_msg = modules.check_uri_parameter_exist(request,uri_parameter)
        if not result:
            dicRet["Response"] = result_msg
            return jsonify( **dicRet)
        
        if not modules.VerifyDataStrLawyer(request.data).verify_json():
            dicRet["Response"] = "error input '{}' is illegal JSON".format(request.data)
            return jsonify( **dicRet)

        reqdataDict = json.loads(request.data)
        if isinstance(reqdataDict,type(u"")):
            reqdataDict = json.loads(reqdataDict)

        post_parameter = ["assignTo","assignToEmail","status","taskInfo","creatorIDName","creatorIDEmail","schedDate","startDate"]
        if not modules.check_post_parameter_exist(reqdataDict,post_parameter):
            dicRet["Response"] = "Missing post parameters : '{}'".format(post_parameter)
            return jsonify( **dicRet)

        emailData = []
        emailData.append({
            "assignTo":reqdataDict.get("assignTo").encode("utf8").strip(),
            "assignToEmail":reqdataDict.get("assignToEmail").encode("utf8").strip(),
            "status":reqdataDict.get("status").encode("utf8").strip(),
            "taskInfo":reqdataDict.get("taskInfo").encode("utf8").strip(),
            "creatorIDName":reqdataDict.get("creatorIDName").encode("utf8").strip(),
            "creatorIDEmail":reqdataDict.get("creatorIDEmail").encode("utf8").strip(),
            "schedDate":reqdataDict.get("schedDate").encode("utf8").strip(),
            "startDate":reqdataDict.get("startDate").encode("utf8").strip()
        })
    
    # send = EmailConfig(selfUse)
    # # send.set_mail_msg(emailData)

    # if send.sendemail(emailData):
    #     print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
    #     print("待辦事項通知郵件傳送成功")
    #     err_msg = "ok"
    # else:
    #     print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
    #     print("待辦事項通知郵件傳送失敗")
    #     err_msg = "fail"

    # if not selfUse:
    #     dicRet["Response"] = err_msg
    #     return jsonify(**dicRet)
    # else:
    #     return err_msg
    from celeryApp.celeryTasks import celery_send_email
    celery_send_email.apply_async(args=(selfUse,emailData), routing_key='low', queue="L-queue1")
    if not selfUse:
        dicRet["Response"] = "郵件已發送"
        return jsonify(**dicRet)
    else:
        return "郵件已發送"

class EmailConfig():
    def __init__(self,overdeadline):
        self.host = app.dicConfig.get("EmailHost")
        self.user = app.dicConfig.get("EmailUser")
        self.password = app.dicConfig.get("EmailPassword")
        self.mail_msg = '<style type="text/css">.tg td{background-color:#EBF5FF;border-color:#9ABAD9;border-style:solid;border-width:0px;color:#444;font-family:Arial, sans-serif;font-size:14px;overflow:hidden;padding:10px 5px;word-break:normal;}\
        .emailtable div{margin:10px;}.tg{border:none;border-collapse:collapse;border-color:#9ABAD9;border-spacing:0;}\
        .tg td{background-color:#EBF5FF;border-color:#9ABAD9;border-style:solid;border-width:0px;color:#444;font-family:Arial, sans-serif;font-size:14px;overflow:hidden;padding:10px 5px;word-break:normal;}\
        .tg th{background-color:#409cff;border-color:#9ABAD9;border-style:solid;border-width:0px;color:#fff;font-family:Arial, sans-serif;font-size:14px;font-weight:normal;overflow:hidden;padding:10px 5px;word-break:normal;}\
        .tg .tg-w2rs{background-color:#ebf5ff;border-color:#9abad9;color:#444444;font-size:100%;text-align:left;vertical-align:top}\
        .tg .tg-0720{background-color:#409cff;color:#ffffff;font-size:15px;font-weight:bold;text-align:left;vertical-align:top}\
        @media screen and (max-width: 767px) {.tg {width: auto !important;}.tg col {width: auto !important;}.tg-wrap {overflow-x: auto;-webkit-overflow-scrolling: touch;}}</style>'
        self.overdeadline = overdeadline
        # print "~~~self.host~~~~"
        # print self.host
        # print "~~~self.user~~~~"
        # print self.user
        # print "~~~self.password~~~~"
        # print self.password
    
    def reset_mail_msg(self):
        self.mail_msg = '<style type="text/css">.tg td{background-color:#EBF5FF;border-color:#9ABAD9;border-style:solid;border-width:0px;color:#444;font-family:Arial, sans-serif;font-size:14px;overflow:hidden;padding:10px 5px;word-break:normal;}\
        .emailtable div{margin:10px;}.tg{border:none;border-collapse:collapse;border-color:#9ABAD9;border-spacing:0;}\
        .tg td{background-color:#EBF5FF;border-color:#9ABAD9;border-style:solid;border-width:0px;color:#444;font-family:Arial, sans-serif;font-size:14px;overflow:hidden;padding:10px 5px;word-break:normal;}\
        .tg th{background-color:#409cff;border-color:#9ABAD9;border-style:solid;border-width:0px;color:#fff;font-family:Arial, sans-serif;font-size:14px;font-weight:normal;overflow:hidden;padding:10px 5px;word-break:normal;}\
        .tg .tg-w2rs{background-color:#ebf5ff;border-color:#9abad9;color:#444444;font-size:100%;text-align:left;vertical-align:top}\
        .tg .tg-0720{background-color:#409cff;color:#ffffff;font-size:15px;font-weight:bold;text-align:left;vertical-align:top}\
        @media screen and (max-width: 767px) {.tg {width: auto !important;}.tg col {width: auto !important;}.tg-wrap {overflow-x: auto;-webkit-overflow-scrolling: touch;}}</style>'
    
    def set_mail_msg(self,maildata):
        self.reset_mail_msg()
        thisTitleName = maildata["assignTo"]
        if self.overdeadline:
            thisTitleName = maildata["assignTo"]+","+maildata["creatorIDName"]
        self.mail_msg += '<div class="emailtable" style="margin:10px">\
        <div>Dear {}：</div>\
        <div>此郵件為待辦事項通知，詳細內容如下：</div>\
        <div class="tg-wrap">\
        <table class="tg" style="undefined;table-layout: fixed; width: 402px">\
        <colgroup><col style="width: 101px"><col style="width: 60vh"></colgroup>\
        <tbody><tr><td class="tg-0720">指派工作項目</td><td class="tg-w2rs">{}</td></tr>\
        <tr><td class="tg-0720">事項狀態</td><td class="tg-w2rs">{}</td></tr>\
        <tr><td class="tg-0720">發起人</td><td class="tg-w2rs">{}</td></tr>\
        <tr><td class="tg-0720">預計完成日期</td><td class="tg-w2rs">{}</td></tr>\
        <tr><td class="tg-0720">發起日</td><td class="tg-w2rs">{}</td></tr></tbody></table></div>\
        <div style="font-size: 10px;color: gray;font-weight: bold;">請勿回覆這封電子郵件。如需詳細資訊，請詢問發起人：{} {}</div></div>'.format(thisTitleName,maildata["taskInfo"],maildata["status"],maildata["creatorIDName"],maildata["schedDate"],maildata["startDate"],maildata["creatorIDName"],maildata["creatorIDEmail"])
    
    def sendemail(self,maildata):
        self.maildata = maildata
        # print "===============in sendemail=================="
        # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
        status = True
        try:
            # print "===============in sendemail 1=================="
            # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
            SMTPserver = smtplib.SMTP(self.host, 587) # 發件人郵箱中的SMTP伺服器

            # print "===============in sendemail 2=================="
            # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
            SMTPserver.ehlo()  # 傳送SMTP 'ehlo' 命令
            SMTPserver.starttls() #加密文件，避免私密信息被截取

            # print "===============in sendemail 3=================="
            # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
            SMTPserver.login(self.user, self.password) # 括號中對應的是發件人郵箱賬號、郵箱密碼

            for data in self.maildata:
                self.set_mail_msg(data)
                msg = MIMEText(self.mail_msg, 'html', 'utf-8')
                msg['From']="{}".format(self.user)
                # msg['From']=formataddr(["SapidoSystem系統管理員","{}".format(self.user)]) # 括號裡的對應收件人郵箱暱稱、收件人郵箱賬號
                # msg['To']=formataddr(["{}".format(data["assignTo"]),"{}".format(data["assignToEmail"])]) # 括號裡的對應收件人郵箱暱稱、收件人郵箱賬號
                # msg['From'] = self._format_addr('SapidoSystem系統管理員<{}>'.format(self.user)) # 發件人郵箱暱稱、發件人郵箱賬號
                # msg['To'] = self._format_addr('{}<{}>'.format(data["assignTo"],data["assignToEmail"])) # 收件人郵箱暱稱、收件人郵箱賬號
                msg['Subject']="待辦事項{}通知".format(data["status"]) # 郵件的主題，也可以說是標題

                # print "~~~~msg~~~~"
                # print msg

                if self.overdeadline:
                    msg['To']="{}".format(data["assignToEmail"]+","+data["creatorIDEmail"])
                    recipients = [data["assignToEmail"],data["creatorIDEmail"]]
                else:
                    msg['To']="{}".format(data["assignToEmail"])
                    recipients = [data["assignToEmail"]]

                # print "~~~~recipients~~~~"
                # print recipients

                # print "===============in sendemail 8=================="
                # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
                
                SMTPserver.sendmail(self.user,recipients,msg.as_string()) # 括號中對應的是發件人郵箱賬號、收件人郵箱賬號、傳送郵件

                # print "===============in sendemail 9=================="
                # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]

        except Exception as e: # 如果 try 中的語句沒有執行，則會執行下面的 ret=False
            # print "===============in sendemail Exception=================="
            # print datetime.now().strftime('%Y-%m-%d %H:%M:%S.%f')[::]
            # print "~~~~e~~~~~"
            # print e
            app.catch_exception(e,sys.exc_info(),"PaaS")
            status = False
        finally:
            if 'SMTPserver' in locals().keys():
                SMTPserver.quit() # 關閉會話
                SMTPserver.close() #關閉SMTP連接

            return status
    
    #=========================================
    #格式化email的头部信息，不然会出错，当做垃圾邮件
    #=========================================
    def _format_addr(self,s):
        name, addr = parseaddr(s)
        # 防止中文问题，进行转码处理，并格式化为str返回
        return formataddr((Header(name,charset="utf-8").encode(),addr.encode("uft-8") if isinstance(addr, unicode) else addr))


@EMAIL_API.route('/api/PaaS/1.0/email/overDeadline', methods=['GET'])
def check_todolist_deadline():
    err_msg = "error"
    dicRet = app.preProcessRequest(request,system="PaaS")
    try:
        DbSession,metadata,engine= app.getDbSessionType(system="SAPIDOSYSTEM")
        if DbSession is None:
            #表示連接資料庫有問題
            raise Exception(engine)
            
        sess = DbSession()

        today = datetime.now().strftime('%Y-%m-%d')

        # if datetime.strptime(timevalue[c][1], "%Y-%m-%d %H:%M:%S") < datetime.strptime(timevalue[c][0], "%Y-%m-%d %H:%M:%S"):

        #先查user config
        user = Table("user" , metadata, autoload=True)
        user_config = {}
        for row in sess.query(getattr(user.c, "uID"),getattr(user.c, "uName"),getattr(user.c, "email")).\
            filter(getattr(user.c, "noumenonID").in_(("1001","1002","1003","1020"))).all():
            drow = modules.AdjustDataFormat().format(row._asdict())
            user_config[drow["uID"]] = {"name":drow["uName"],"email":drow["email"]}
            
        over_deadline = []
        todoList = Table("todoList" , metadata, autoload=True)
        for row in sess.query(getattr(todoList.c, "taskInfo"),getattr(todoList.c, "schedDate"),\
            getattr(todoList.c, "assignTo"),getattr(todoList.c, "startDate"),getattr(todoList.c, "creatorID")).\
            filter(and_(getattr(todoList.c, "status") == 0,today > getattr(todoList.c, "schedDate"))).all():
            # filter(and_(getattr(todoList.c, "status") == 0,today > getattr(todoList.c, "schedDate"),getattr(todoList.c, "assignTo") == 2493)).all():
            drow = modules.AdjustDataFormat().format(row._asdict())
            # print "~~~~drow~~~~"
            # print drow
            over_deadline.append({
                "assignTo":user_config[drow["assignTo"]]["name"],
                "assignToEmail":user_config[drow["assignTo"]]["email"],
                "status":"已逾期",
                "taskInfo":drow["taskInfo"],
                "creatorIDName":user_config[drow["creatorID"]]["name"],
                "creatorIDEmail":user_config[drow["creatorID"]]["email"],
                "schedDate":drow["schedDate"],
                "startDate":drow["startDate"],
            })
    
        err_msg = sending_email(over_deadline,True)
        if err_msg != "ok":
            raise Exception(err_msg)

        # err_msg = "ok" #done successfully
        # # http://stackoverflow.com/questions/4112337/regular-expressions-in-sqlalchemy-queries
    except Exception as e:
        err_msg = app.catch_exception(e,sys.exc_info(),"PaaS")

    finally:
        if 'DbSession' in locals().keys() and DbSession is not None:
            sess.close()
            DbSession.remove()
            engine.dispose()

    dicRet['Response'] = err_msg
    return jsonify( **dicRet)