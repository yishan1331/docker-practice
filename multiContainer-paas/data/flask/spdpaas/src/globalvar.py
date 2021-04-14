# -*- coding: utf-8 -*-
#Description
"""
==============================================================================
created    : 07/17/2020

Last update: 07/17/2020

Developer: Yishan Tsai 

Lite Version 1 @Yishan05212020

Filename: globalvar.py

Description: global variable
==============================================================================
"""

SERVERIP = "serverKSS"

#支援使用的系統列表
global SYSTEMLIST
SYSTEMLIST = {
    "server71": ["IOT","APIDOC"],
    "server75": ["SAPIDOSYSTEM","OQA","APIDOC"],
    "serverKSS": ["OQA","APIDOC"]
}
#------------------------------------------------------
#Wei@10022018 adding the if-else for customer check
#For runnable version, the uid check for administor 
# of service, should be added to the database and set it up 
# when deployed to client
#支援使用uid列表
#---------------------------------------------------------
global XXXUSER
XXXUSER = {
    "server71": ["@sapido@PaaS","AOItest"],
    "server75": ["@sapido@PaaS"],
    "serverKSS": ["@sapido@PaaS"]
}
#各資料庫固定時間欄位
global CREATETIME
CREATETIME = {
    "server71": {"mysql":"created_at","mssql":"created_at"},
    "server75": {"mysql":"createTime","mssql":"created_at"},
    "serverKSS": {"mysql":"createTime","mssql":"created_at"}
}
global UPLOADTIME
UPLOADTIME = {
    "server71": {"mysql":"updated_at","mssql":"update_at","postgres":"upload_at"},
    "server75": {"mysql":"lastUpdateTime","postgres":"upload_at","mssql":"update_at"},
    "serverKSS": {"mysql":"lastUpdateTime","postgres":"upload_at","mssql":"update_at"}
}

#logging模組要查看的log file列表
global LOGFILELIST
LOGFILELIST = {"uWSGI_LOG":{"filename":"spdpaas_uwsgi.log","path":"/var/log/uwsgi/"},"PaaS_LOG":{"filename":"log_sapidoPaaS","path":"/var/www/spdpaas/log/"}}

#SqlSyntax api的條件參數
global SQLSYNTAX_PARAMETER
SQLSYNTAX_PARAMETER = {"table":str,"fields":list,"where":dict,"orderby":list,"limit":list,"symbols":dict,"intervaltime":dict,"subquery":dict,"union":list}
global JOIN_SQLSYNTAX_PARAMETER
JOIN_SQLSYNTAX_PARAMETER = {"tables":list,"fields":dict,"where":dict,"join":dict,"jointype":dict,"orderby":list,"limit":list,"symbols":dict,"subquery":dict}
global SQLSYNTAX_OPERATOR
SQLSYNTAX_OPERATOR = ["equal","notequal","greater","less","leftlike","like","rightlike","in","notin"]
global SQLSYNTAX_SUBQUERY_SYMBOL
SQLSYNTAX_SUBQUERY_SYMBOL = {"in":"subin","notin":"subnotin"}

#readConfig參數
DBCONFIG = {
    "MYSQL":"DBMYSQL",
    "POSTGRESQL":"DBPOSTGRES",
    "REDIS":"DBREDIS",
    "MSSQL":"DBMSSQL"
}
CONFIG = {
    #DB config
    "SYSTEM":{
        #DB
        "MYSQL":"DataBaseMysql",
        "POSTGRESQL":"DataBasePostgresql",
        "REDIS":"DataBaseRedis",
        #Feature
        "APIDOC":"Mysql_APIDOC",
        "PaaS":"Mysql_PaaS"
    },
    #diff ip dbname
    "server71": {
        "IOT":{
            "MYSQL":["Mysql_IOT"],
            "MSSQL":["Mssql_IOT"]
        }
    },
    "server75": {
        "SAPIDOSYSTEM":{
            "MYSQL":["Mysql_SAPIDOSYSTEM"],
            "Email":"Email"
        },
        "OQA":{
            "MSSQL":["Mssql_OQA"]
        }
    },
    "serverKSS": {
        "OQA":{
            "MYSQL":["Mysql_OQA"],
            "MSSQL":["Mssql_OQA","Mssql_IOT"]
        }
    }
}