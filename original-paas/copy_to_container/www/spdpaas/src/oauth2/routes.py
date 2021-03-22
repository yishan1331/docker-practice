# -*- coding: utf-8 -*-
import sys
#wei@02262019
reload(sys)
sys.setdefaultencoding('utf-8')
import time, json

from flask import request, jsonify, Blueprint

from werkzeug.security import gen_salt
from authlib.integrations.flask_oauth2 import current_token
from authlib.oauth2 import OAuth2Error

from .models import SystemList, OAuth2Client
from .oauth2 import require_oauth
from apiPortal import authorization
import modules
print "........authorization......"
print authorization

SYSTEM = "PaaS"

#blueprint
OAUTH2_API = Blueprint('OAUTH2_API', __name__)

#=======================================================
# API: /api/PaaS/1.0/oauth2/CreateSystem
# Definition: 提供客戶新增API主帳號，回傳client_id & client_secret（期限預設n秒）
# Remark: HTTP Content-Type must to be a "application/json"
#=======================================================
#{{{ @OAUTH2_API.route('/api/PaaS/1.0/oauth2/CreateSystem', methods=['POST'])
@OAUTH2_API.route('/api/PaaS/1.0/oauth2/CreateSystem', methods=['POST'])
@modules.decorator_check_content_type(request)
def oauth2_create_system():
    err_msg = "error"
    dicRet = app.preProcessRequest(request,system=SYSTEM)
    reqdataDict = json.loads(request.data)
    # print "~~~~request~~~~"
    # print reqdataDict
    system_name = reqdataDict.get("system_name")
    system_info = reqdataDict.get("system_info")

    try:
        DbSession,_,engine= app.getDbSessionType(system=SYSTEM)
        if DbSession is None:
            #表示連接資料庫有問題
            dicRet["Response"] = engine
            return jsonify( **dicRet)
        
        sess = DbSession()

        system = sess.query(SystemList).filter_by(system_name=system_name).first()
        if not system:
            system = SystemList(system_name=system_name,system_info=system_info)
            sess.add(system)
            sess.commit()
        
        client_id = gen_salt(24)
        client_id_issued_at = int(time.time())
        client = OAuth2Client(
            client_id=client_id,
            client_id_issued_at=client_id_issued_at,
            system_id=system.id,
            client_secret_expires_at=600
        )
        # print "~~~~client~~~~~"
        # print client

        client_metadata = {
            "client_name": system_name,
            "client_uri": "",
            "grant_types": ["client_credentials"],
            "redirect_uris": [],
            "response_types": ["code"],
            "scope": "",
            "token_endpoint_auth_method": "client_secret_basic"
        }
        # print "=====client_metadata====="
        # print client_metadata
        client.set_client_metadata(client_metadata)

        if reqdataDict.get('token_endpoint_auth_method') == 'none':
            client.client_secret = ''
        else:
            client.client_secret = gen_salt(48)

        sess.add(client)
        sess.commit()

        err_msg = "ok"
        dicRet["Oauth2Data"] = {"client_id":client_id,"client_secret":client.client_secret}

    except Exception as e:
        err_msg = app.catch_exception(e,sys.exc_info(),SYSTEM)

    finally:
        if 'DbSession' in locals().keys() and DbSession is not None:
            sess.close()
            DbSession.remove()
            engine.dispose()
    
    dicRet["Response"] = err_msg
    return jsonify( **dicRet)
#}}}

#=======================================================
# API: /api/PaaS/1.0/oauth2/UpdateClientSecret
# Definition: 提供客戶更新client_secret的期限（期限n秒）
#=======================================================
#{{{ @OAUTH2_API.route('/api/PaaS/1.0/oauth2/UpdateClientSecret', methods=['POST'])
@OAUTH2_API.route('/api/PaaS/1.0/oauth2/UpdateClientSecret', methods=['POST'])
# @modules.DecoratorOauth2ConnectDB(False) #利用裝飾器檢查
def oauth2_update_client_secret():
    #為避免使用者使用其他grant_type，在此將request.form add key->grant_type:client_credentials
    #因request.from 為ImmutableMultiDict，須先將其轉成dict
    request.form = request.form.to_dict()
    request.form["grant_type"] = "client_credentials"
    return authorization.update_client_secret_func()
#}}}

#=======================================================
# API: /api/PaaS/1.0/oauth2/ApplyToken
# Definition: 提供客戶以主帳號的client_id & client_secret建立子帳號(token)，可限定n個
# Remark: HTTP Content-Type must to be a "application/x-www-form-urlencoded"
#=======================================================
#{{{ @OAUTH2_API.route('/api/PaaS/1.0/oauth2/ApplyToken', methods=['POST'])
@OAUTH2_API.route('/api/PaaS/1.0/oauth2/ApplyToken', methods=['POST'])
# @modules.DecoratorOauth2ConnectDB(False) #利用裝飾器檢查
@modules.decorator_check_content_type(request,"application/x-www-form-urlencoded")
def oauth2_apply_token():
    from authlib.oauth2.rfc6750 import BearerToken
    BearerToken.GRANT_TYPES_EXPIRES_IN["client_credentials"] = 300
    #為避免使用者使用其他grant_type，在此將request.form add key->grant_type:client_credentials
    #因request.from 為ImmutableMultiDict，須先將其轉成dict
    request.form = request.form.to_dict()
    request.form["grant_type"] = "client_credentials"
    return authorization.create_token_response()
#}}}

#=======================================================
# API: /api/PaaS/1.0/oauth2/RevokeToken
# Definition: 提供客戶以主帳號的client_id & client_secret撤銷子帳號(token)
# Remark: HTTP Content-Type must to be a "application/x-www-form-urlencoded"
#=======================================================
#{{{ @OAUTH2_API.route('/api/PaaS/1.0/oauth2/RevokeToken', methods=['POST'])
@OAUTH2_API.route('/api/PaaS/1.0/oauth2/RevokeToken', methods=['POST'])
# @modules.DecoratorOauth2ConnectDB(False) #利用裝飾器檢查
@modules.decorator_check_content_type(request,"application/x-www-form-urlencoded")
def revoke_token():
    return authorization.create_endpoint_response('revocation')
#}}}



@OAUTH2_API.route('/api/PaaS/1.0/test/scope/A')
# @require_oauth('profile API_A', 'OR')
# @modules.DecoratorOauth2ConnectDB() #利用裝飾器檢查
@require_oauth()
def api_scope_A():
    return jsonify(response="ok")

@OAUTH2_API.route('/api/PaaS/1.0/test/scope/B')
# @require_oauth('API_B',optional=True)
# @modules.DecoratorOauth2ConnectDB() #利用裝飾器檢查
@require_oauth('B')
def api_scope_B():
    return jsonify(response="ok")