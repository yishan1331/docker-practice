# -*- coding: utf-8 -*-
#User module description
"""
==============================================================================
created    : 
Last update: 03/31/2021
Developer: Wei-Chun Chang 
Lite Version 2 @Yishan08212019
API Version 1.0

Filename: userHandle.py

Description: 

Total = 3 APIs
* wei@03272018 add an API(/api/SAPIDOSYSTEM/query/tableData): to retrieve all data in a table
==============================================================================
"""

#=======================================================
# System level modules
#=======================================================
# {{{
import sys
#wei@02262019
reload(sys)
sys.setdefaultencoding('utf-8')
import traceback, json

from flask import request, jsonify, Blueprint

import string
from random import randint,choice,sample
#08382019@Yishan add for 加密password
from Crypto.Cipher import AES
from binascii import b2a_hex, a2b_hex
from sqlalchemy import func  #sandy 05292019 without ignoring cases
# }}}

#=======================================================
# User-defined modules
#=======================================================
# {{{
from dbModel.sapidosystem import User,Department
from apiPortal import app
import globalvar
import modules
# import web_utility
# import authentic_utility
# }}}

ACCESS_SYSTEM_LIST = ["SAPIDOSYSTEM"]

#blueprint
USER_API = Blueprint('USER_API', __name__)

#=======================================================
# Definition: To retrieve sensor raw table name and other info.
# reserve for future usage
# return queryRec[] = [noumenonID, noumenonType, senRawTable, creatorID]
#=======================================================
#{{{ def _retrieve_senRawTable(sess, senID)
def _retrieve_senRawTable(sess,tableID,del_ID):
    if tableID == "Department":
        queryRecs = sess.query(Department.noumenonID,\
                    Department.noumenonType, \
                    Department.dbName,\
                    Department.depID). \
            filter(Department.depID == func.binary(del_ID)).\
            all()
    else:
        return ""

    if len(queryRecs) == 0:
        return "" #no ID has found
    else:
        return queryRecs[0]
#}}}

#=======================================================
# method to generate a user ID composite with 3 fields:
# 1. user name
# 2. Noumenon which a sensor is rooted
# 3. random int between 8 to 10 digits
#=======================================================
# {{{ def _generate_userID(userName,Noumenon):
def _generate_userID(sess):
    #vednor namingregualtion: userName_userDomain_time_randomNumber    
    #return "{}_{}_{}".format(uname,Noumenon, ''.join(choice(string.digits) for _ in range(randint(8,10))))
    condition = True
    uid = ""
    chars = string.ascii_uppercase + string.ascii_lowercase
    while condition:
        #uid = "{}_{}_{}".format(uname,Noumenon, ''.join(choice(string.digits) for _ in range(randint(8,10))))
        uid = "{}_{}".format(u''.join(choice(chars) for _ in range(randint(3,6))), u''.join(choice(string.digits) for _ in range(randint(4,7))))
        queryRecs = sess.query(User.uid).    \
                filter(User.uid == uid).  \
                all()
        if len(queryRecs) == 0: #No redundant id in the DB
            condition = False
    return uid
# }}}

#=======================================================
# method to generate a random password for a user
#=======================================================
# {{{ def generate Pwd Str():
def _generate_pwdstr():
    #8-10 chars mix with digits    
    return "{}".format(u''.join(choice(string.ascii_letters + string.digits) for _ in range(randint(6,10))))
#}}}

#=======================================================
# method to generate a random str for AES IV
#=======================================================
#{{{ def _get_random()
def _get_random(size):
    return "".join(sample(string.letters+string.digits,size))
# }}}

#=======================================================
# API to register a user{uname, noumenon, phone,uinfo}
# system will generate uid and pwd for user
# FOR MYSQL
#=======================================================
# {{{ app.route('/api/<SYSTEM>/1.0/my/user/reg_User', methods = ['POST'])
@USER_API.route('/api/<SYSTEM>/1.0/my/user/reg_User', methods = ['POST'])
@modules.docstring_parameter(ACCESS_SYSTEM_LIST=ACCESS_SYSTEM_LIST)
def MYSQL_reg_User(SYSTEM):
    #{{{APIINFO
    '''
    {
        "API_application":"提供註冊mysql一使用者帳號資料服務",
        "API_parameters":{"uid":"使用者帳號"},
        "API_path_parameters":{"SYSTEM":"合法的系統名稱"},
        "API_postData":{
            "bodytype":"Object",
            "bodyschema":"{}",
            "parameters":{
                "uid":{"type":"String","requirement":"required","directions":"使用者帳號","example":"test"},
                "pwd":{"type":"String","requirement":"required","directions":"使用者密碼，不超過16 bytes，以免加密後密碼長度太長","example":"1234"},
                "uname":{"type":"String","requirement":"required","directions":"使用者名稱","example":"test"},
                "uinfo":{"type":"String","requirement":"required","directions":"使用者描述","example":"test"},
                "email":{"type":"String","requirement":"required","directions":"使用者公司信箱","example":"test@sapido.com.tw"},
                "noumenonType":{"type":"String","requirement":"required","directions":"隸屬類別","example":"site4"},
                "noumenonID":{"type":"String","requirement":"required","directions":"隸屬編號","example":"site4"},
                "accessList":{"type":"String","requirement":"required","directions":"個人權限編號","example":{"mes":true}},
                "creatorID":{"type":"String","requirement":"required","directions":"創建者ID","example":"site4"}
            }
        },
        "API_message_parameters":{"uid":"string","DB":"string"},
        "API_example":{
            "APIS": "POST /api/SAPIDOSYSTEM/1.0/my/user/reg_User",
            "OperationTime": "0.033",
            "Response": "ok",
            "BytesTransferred": 76,
            "uid":"-----",
            "DB":"MYSQL"
        }
    }
    '''
    #}}}
    #-----------------------------------------------------------
    # method=POST a user 
    # wei@03152017 temporarily disable
    # for uid & pwd string checking
    # dicRet = app.preProcessRequest(request,system="SAPIDOSYSTEM")
    #-----------------------------------------------------------
    err_msg = "error"
    print "~~~~SYSTEM~~~~"
    print SYSTEM
    dicRet = app.preProcessRequest(request,system=SYSTEM)
    if SYSTEM not in list(set(globalvar.SYSTEMLIST[globalvar.SERVERIP]).intersection(set(ACCESS_SYSTEM_LIST))):
        dicRet["Response"] = "system:{} has no privillege to use this API".format(SYSTEM)
        return jsonify( **dicRet)

    uri_parameter = ["uid"]
    result, result_msg = modules.check_uri_parameter_exist(request,uri_parameter)
    if not result:
        dicRet["Response"] = result_msg
        return jsonify( **dicRet)

    if not modules.VerifyDataStrLawyer(request.data).verify_json():
        dicRet["Response"] = "error input '{}' is illegal JSON".format(request.data)
        return jsonify( **dicRet)

    #collect data items from a request
    reqdataDict = json.loads(request.data)
    if isinstance(reqdataDict,type(u"")):
        reqdataDict = json.loads(reqdataDict)

    # retrieve user info from request followed the schema of table 'user' 
    # scenario: avoid to have identical user re-generate from ID generation
    # we need to check ID from DB first, if exist, then re-generate
    # another issue: what happen if "update A user" is added to the API?
    # possible to have a message from client to indicate it's a update or register operation
    # 04132017: need to double check if ulevel exists? Normally, the ulevel will come from client side
    # which was obtained by querying to PaaS server

    post_parameter = ["uID","pwd","uName","uInfo","email","noumenonType","noumenonID","accessList","creatorID"]
    if not modules.check_post_parameter_exist(reqdataDict,post_parameter):
        dicRet["Response"] = "Missing post parameters : '{}'".format(post_parameter)
        return jsonify( **dicRet)    

    uID = reqdataDict.get("uID").encode('utf8').strip()
    pwd = reqdataDict.get("pwd").encode('utf8').strip()
    userName = reqdataDict.get("uName").encode('utf8').strip()

    err_msg = "Error" #reset error message
    try:
        #fill-in to object User for insertion
        DbSession,_,engine= app.getDbSessionType(system=SYSTEM)
        if DbSession is None:
            #表示連接資料庫有問題
            dicRet["Response"] = engine
            return jsonify( **dicRet)
        
        sess = DbSession()

        #wei@04082019 change from uname to uid 

        queryRecs = sess.query(User.uID).\
                filter(User.uID == func.binary(uID)).\
                all()

        if len(queryRecs) == 0:
            #generate userID & password
            #with checking in the method, can quanrantee a unique user ID is generated
            #userID = _generate_userID(sess)
            #pwdStr = _generate_pwdstr()
            #wei@01302019 switch to new policy, uid & pwd will be the same with user registration
            userID = str(uID)
            pwdStr = pwd

            if len(pwdStr) > 16:
                dicRet["Response"] = "length of pwd need smaller than 16"
                return jsonify( **dicRet)

            AES_IV = uID
            AES = modules.Prpcrypt(app.dicConfig.get('aes_key'),AES_IV,SYSTEM)
            status,AESEN_PWD = AES.encrypt(pwdStr)
            if not status:
                dicRet["Response"] = AESEN_PWD
                return jsonify( **dicRet)

            newUserRec = User(uID = userID,\
                #pwd = pwdStr,\
                pwd = AESEN_PWD,\
                uName = userName,\
                uInfo = reqdataDict.get("uInfo").encode('utf8').strip(),\
                email = reqdataDict.get("email").encode('utf8').strip(),\
                noumenonType = reqdataDict.get("noumenonType").encode('utf8').strip(),\
                noumenonID = reqdataDict.get("noumenonID").encode('utf8').strip(),\
                accessList = reqdataDict.get("accessList"),\
                creatorID = reqdataDict.get("creatorID").encode('utf8').strip())

            sess.add(newUserRec)
            err_msg = "ok" #done successfully:
        else:
            #update user data here
            userID = "-------"
            pwdStr = "-------"
            err_msg = "Error: user existed"
        #end of if len(queryRecs)
        #commit insertion
        sess.commit()

    except Exception as e:
        err_msg = app.catch_exception(e,sys.exc_info(),SYSTEM)

    finally:
        if 'DbSession' in locals().keys() and DbSession is not None:
            sess.close()
            DbSession.remove()
            engine.dispose()

    #end of if-check err_msg
    dicRet["uID"] = userID
    dicRet["Response"] = err_msg 
    dicRet["DB"] = "MYSQL"

    return jsonify( **dicRet)
# }}}

#=======================================================
# API to update a user{custID, cust_name, contID, countID, address, postcode}
# FOR MYSQL
#=======================================================
# {{{ app.route('/api/<SYSTEM>/1.0/my/user/update_User', methods = ['PATCH'])
@USER_API.route('/api/<SYSTEM>/1.0/my/user/update_User', methods = ['PATCH'])
@modules.docstring_parameter(ACCESS_SYSTEM_LIST=ACCESS_SYSTEM_LIST)
def MYSQL_update_User(SYSTEM):
    #{{{APIINFO
    '''
    {
        "API_application":"提供更新mysql一使用者基本資料服務",
        "API_parameters":{"uid":"使用者帳號"},
        "API_path_parameters":{"SYSTEM":"合法的系統名稱"},
        "API_postData":{
            "bodytype":"Object",
            "bodyschema":"{}",
            "parameters":{
                "old_uid":{"type":"String","requirement":"required","directions":"原始使用者帳號","example":"test"},
                "new_uid":{"type":"String","requirement":"required","directions":"更新使用者帳號","example":"test2"},
                "pwd":{"type":"String","requirement":"required","directions":"使用者密碼，不超過16 bytes，以免加密後密碼長度太長","example":"2222"},
                "uname":{"type":"String","requirement":"required","directions":"使用者名稱","example":"test"},
                "uinfo":{"type":"String","requirement":"required","directions":"使用者描述","example":"test"},
                "email":{"type":"String","requirement":"required","directions":"使用者公司信箱","example":"test@sapido.com.tw"},
                "noumenonType":{"type":"String","requirement":"required","directions":"隸屬類別","example":"site4"},
                "noumenonID":{"type":"String","requirement":"required","directions":"隸屬編號","example":"site4"},
                "accessList":{"type":"String","requirement":"required","directions":"個人權限編號","example":"site4"}
            }
        },
        "API_message_parameters":{"userID":"string","userName":"string","DB":"string"},
        "API_example":{
            "APIS": "PATCH /api/SAPIDOSYSTEM/1.0/my/user/update_User",
            "OperationTime": "0.033",
            "Response": "ok",
            "BytesTransferred": 76,
            "userID":"AP3",
            "userName":"aaa",
            "DB":"MYSQL"
        }
    }
    '''
    #}}}
    err_msg = "error"
    dicRet = app.preProcessRequest(request,system=SYSTEM)
    if SYSTEM not in list(set(globalvar.SYSTEMLIST[globalvar.SERVERIP]).intersection(set(ACCESS_SYSTEM_LIST))):
        dicRet["Response"] = "system:{} has no privillege to use this API".format(SYSTEM)
        return jsonify( **dicRet)

    uri_parameter = ["uid"]
    result, result_msg = modules.check_uri_parameter_exist(request,uri_parameter)
    if not result:
        dicRet["Response"] = result_msg
        return jsonify( **dicRet)
    
    if not modules.VerifyDataStrLawyer(request.data).verify_json():
        dicRet["Response"] = "error input '{}' is illegal JSON".format(request.data)
        return jsonify( **dicRet)

    #collect data items from a request
    reqdataDict = json.loads(request.data)
    if isinstance(reqdataDict,type(u"")):
        reqdataDict = json.loads(reqdataDict)

    post_parameter = ["old_uID","new_uID","pwd","uName","uInfo","email","noumenonType","noumenonID","accessList","creatorID"]
    if not modules.check_post_parameter_exist(reqdataDict,post_parameter):
        dicRet["Response"] = "Missing post parameters : '{}'".format(post_parameter)
        return jsonify( **dicRet)  

    ouID = reqdataDict.get("old_uID").encode('utf8').strip()
    nuID = reqdataDict.get("new_uID").encode('utf8').strip()
    username = reqdataDict.get("uName").encode('utf8').strip()
    pwd = reqdataDict.get("pwd").encode('utf8').strip()
            
    try:
        DbSession,_,engine= app.getDbSessionType(system=SYSTEM)
        if DbSession is None:
            #表示連接資料庫有問題
            dicRet["Response"] = engine
            return jsonify( **dicRet)
        
        sess = DbSession()

        queryRecs = sess.query(User.uID).    \
                filter(User.uID == func.binary(ouID)).  \
                all()

        if len(queryRecs) == 0:
            err_msg = "user does not exist!"
        else:
            if len(pwd) > 16:
                err_msg = "length of pwd need smaller than 16"
                dicRet["Response"] = err_msg
                return jsonify( **dicRet)

            AES_IV = str(nuID)
            AES = modules.Prpcrypt(app.dicConfig.get('aes_key'),AES_IV,SYSTEM)
            status,AESEN_PWD = AES.encrypt(pwd)
            if not status:
                dicRet["Response"] = AESEN_PWD
                return jsonify( **dicRet)

            sess.query(User.uID, User.pwd, User.uName, User.uInfo, User.email,\
                        User.noumenonType,User.noumenonID,User.accessList,User.creatorID).\
                filter(User.uID == ouID).\
                update({"uID": nuID, \
                        "pwd": AESEN_PWD, \
                        "uName":username, \
                        "uInfo":reqdataDict.get("uInfo").encode('utf8').strip(), \
                        "email":reqdataDict.get("email").encode('utf8').strip(), \
                        "noumenonType": reqdataDict.get("noumenonType").encode('utf8').strip(), \
                        "noumenonID": reqdataDict.get("noumenonID").encode('utf8').strip(), \
                        "accessList":reqdataDict.get("accessList"), \
                        "creatorID":reqdataDict.get("creatorID").encode('utf8').strip()})
            err_msg = "ok" #done successfully
            #commit insertion
            sess.commit()
    
    except Exception as e:
        err_msg = app.catch_exception(e,sys.exc_info(),SYSTEM)

    finally:
        if 'DbSession' in locals().keys() and DbSession is not None:
            sess.close()
            DbSession.remove()
            engine.dispose()

    dicRet["userID"] = nuID
    dicRet["userName"] = username
    dicRet["Response"] = err_msg 
    dicRet["DB"] = "MYSQL"

    return jsonify( **dicRet)
# }}}

#=======================================================
# method to delete a user profile
# uid-> user with priority to operate this API
# del_uid -> uid to be deleted
# FOR MYSQL
#=======================================================
#{{{ @app.route('/api/<SYSTEM>/1.0/my/user/delete_user', methods=['DELETE'])
@USER_API.route('/api/<SYSTEM>/1.0/my/user/delete_User', methods=['DELETE'])
@modules.docstring_parameter(ACCESS_SYSTEM_LIST=ACCESS_SYSTEM_LIST)
def MYSQL_delete_a_user(SYSTEM):
    #{{{APIINFO
    '''
    {
        "API_application":"提供刪除mysql一使用者基本資料的服務",
        "API_parameters":{"uid":"使用者帳號"},
        "API_path_parameters":{"SYSTEM":"合法的系統名稱"},
        "API_postData":{
            "bodytype":"Object",
            "bodyschema":"{}",
            "uID":{"type":"String","requirement":"required","directions":"欲刪除的uID","example":"test"}
        },
        "API_message_parameters":{"userID":"string","DB":"string"},
        "API_example":{
            "APIS": "DELETE /api/SAPIDOSYSTEM/1.0/my/user/delete_User",
            "OperationTime": "0.021",
            "Response": "ok",
            "BytesTransferred": 683,
            "userID":"v002",
            "DB":"MYSQL"
        }
    }
    '''
    #}}}
    err_msg = "error"
    dicRet = app.preProcessRequest(request,system=SYSTEM)
    if SYSTEM not in list(set(globalvar.SYSTEMLIST[globalvar.SERVERIP]).intersection(set(ACCESS_SYSTEM_LIST))):
        dicRet["Response"] = "system:{} has no privillege to use this API".format(SYSTEM)
        return jsonify( **dicRet)
    
    uri_parameter = ["uid"]
    result, result_msg = modules.check_uri_parameter_exist(request,uri_parameter)
    if not result:
        dicRet["Response"] = result_msg
        return jsonify( **dicRet)
    
    reqdataDict = json.loads(request.data)
    if isinstance(reqdataDict,type(u"")):
        reqdataDict = json.loads(reqdataDict)
    
    post_parameter = ["uID"]
    if not modules.check_post_parameter_exist(reqdataDict,post_parameter):
        dicRet["Response"] = "Missing post parameters : '{}'".format(post_parameter)
        return jsonify( **dicRet)    

    del_uID = reqdataDict.get("uID")

    try:
        #fill-in to object User for insertion
        DbSession,_,engine= app.getDbSessionType(system=SYSTEM)
        if DbSession is None:
            #表示連接資料庫有問題
            dicRet["Response"] = engine
            return jsonify( **dicRet)
        
        sess = DbSession()

        #wei@10032018 to execute update
        #check user id- use old ID if exists
        queryRecs = sess.query(User.uID).    \
                filter(User.uID == func.binary(del_uID)).  \
                all()

        if len(queryRecs) == 0:
            err_msg = "user does not exist!"
        else:
            sess.query(User.uID).\
                filter(User.uID == del_uID).\
                delete()
            err_msg = "ok" #done successfully

        #commit deletion
        sess.commit()
    
    except Exception as e:
        err_msg = app.catch_exception(e,sys.exc_info(),SYSTEM)

    finally:
        if 'DbSession' in locals().keys() and DbSession is not None:
            sess.close()
            DbSession.remove()
            engine.dispose()

    dicRet["userID"] = del_uID
    dicRet["Response"] = err_msg
    dicRet["DB"] = "MYSQL" 

    return jsonify( **dicRet)
# }}}