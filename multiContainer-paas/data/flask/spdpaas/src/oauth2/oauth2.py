# -*- coding: utf-8 -*-
import sys
from authlib.integrations.flask_oauth2 import (
    AuthorizationServer,
    ResourceProtector,
)
#Yishan add
from authlib.integrations.sqla_oauth2 import (
    create_query_client_func,
    create_save_token_func,
    create_revocation_endpoint,
    create_bearer_token_validator,
    update_client_secret_func
)

from authlib.oauth2.rfc6749 import grants
from authlib.oauth2.rfc7636 import CodeChallenge

import redis
# from .models import db,Yishan_dbRedis
from .models import OAuth2Client, OAuth2Token

require_oauth = ResourceProtector()

def config_oauth(app):

    DbSession,metadata,engine= app.getDbSessionType(system="PaaS")
    if DbSession is not None:
        sess = DbSession()
        POOL = redis.ConnectionPool(host="127.0.0.1",port="6379",db=15,password="sapido")
        Yishan_dbRedis = redis.Redis(connection_pool=POOL)

        #Yishan add
        update_client_secret = update_client_secret_func(sess, OAuth2Client)
        query_client = create_query_client_func(sess, OAuth2Client)
        save_token = create_save_token_func(sess, OAuth2Token, OAuth2Client, Yishan_dbRedis)
        authorization = AuthorizationServer(
            query_client=query_client,
            save_token=save_token,
            update_client_secret=update_client_secret
        )

        authorization.init_app(app)

        # support all grants
        # authorization.register_grant(grants.ImplicitGrant)
        authorization.register_grant(grants.ClientCredentialsGrant)

        # support revocation
        revocation_cls = create_revocation_endpoint(sess, OAuth2Token,Yishan_dbRedis)
        authorization.register_endpoint(revocation_cls)

        # protect resource
        bearer_cls = create_bearer_token_validator(sess, OAuth2Token, OAuth2Client, Yishan_dbRedis)
        require_oauth.register_token_validator(bearer_cls())

        return sess,DbSession,engine,Yishan_dbRedis,authorization