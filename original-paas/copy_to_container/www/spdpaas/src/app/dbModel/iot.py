# -*- coding: utf-8 -*-
#!/usr/bin/env python2.7
#sql_utility Module Description
"""
==============================================================================
created    : 03/23/2017
Last update: 04/21/2017
Developer: Wei-Chun Chang 
Lite Version 2 @Yishan08032019
API Version 1.0
 
Filename: sql_utility.py
Description: basically, all writes to the module will be opened to users who has authorized 
    1. register a sensor 
    2. query a sensor's basic info.
    3. query sensor raw data
       queryFirst/queryLast/queryRows
    4. post sensor raw data
Total = 2 APIs
==============================================================================
"""

#=======================================================
#system level modules
#=======================================================
# {{{
import sys, hashlib
from sqlalchemy import Column, Integer, Float, Boolean, String, Text, TIMESTAMP, DateTime, JSON, Date, Numeric
from sqlalchemy.orm import mapper
from sqlalchemy.ext.declarative import declarative_base
import sqlalchemy.types as types
from sqlalchemy.sql import func
# }}}

#=======================================================
#global-defined variables
#=======================================================
# {{{
#Construct a base class for declarative class definitions.
Base = declarative_base()
# }}}

#=======================================================
# special API to generate Raw Data DB 
# a privileges for super user
#=======================================================
# {{{ def genRawDBName(depID):
def genRawDBName(dbID, typeName):
        return typeName + "_" + hashlib.md5(dbID).hexdigest()[:29]
# }}}

# {{{ class Sensor(Base)
class Sensor(Base):
    __tablename__ = 'sensor'
    id = Column(Integer, primary_key=True)
    name = Column(String)
    sensor_type = Column(Boolean)
    noumenon_type = Column(String)
    noumenon_id = Column(Integer)
    sensor_raw_table = Column(String)
    sensor_attr = Column(JSON)
    note = Column(String)
    creator = Column(Integer)
    modifier = Column(Integer)
    created_at = Column(DateTime) 
    updated_at = Column(DateTime)
# }}}

# class LxmlXMLType(types.UserDefinedType):
#     from lxml import etree
#     def get_col_spec(self):
#         return 'XML'

#     def bind_processor(self, dialect):
#         def process(value):
#             if value is not None:
#                 if isinstance(value, str):
#                     return value
#                 else:
#                     return etree.tostring(value)
#             else:
#                 return None
#         return process

#     def result_processor(self, dialect, coltype):
#         def process(value):
#             if value is not None:
#                 value = etree.fromstring(value)
#             return value
#         return process
#https://stackoverflow.com/questions/43333410/sqlalchemy-declaring-custom-userdefinedtype-for-xml-in-mssql

class ElementTreeXMLType(types.UserDefinedType):
    import xml.etree.ElementTree as etree
    def get_col_spec(self):
        return 'XML'

    def bind_processor(self, dialect):
        def process(value):
            if value is not None:
                if isinstance(value, str):
                    return value
                else:
                    return etree.tostring(value)
            else:
                return None
        return process

    def result_processor(self, dialect, coltype):
        def process(value):
            if value is not None:
                value = etree.fromstring(value)
            return value
        return process
#https://stackoverflow.com/questions/16153512/using-postgresql-xml-data-type-with-sqlalchemy

#=======================================================
# ??????????????????5?????????????????????????????????
# Date: 01112021@Yishan
# https://stackoverflow.com/questions/54646044/sqlalchemy-tablename-as-a-variable
#=======================================================
#{{{ def create_default_device_models(device_serial_number):
def create_default_device_models(device_serial_number):
    class DeviceMain(Base):
        __tablename__ = device_serial_number+"_main"

        upload_at = Column(DateTime, primary_key=True, server_default=func.current_timestamp(6))
        mcks_op_temp = Column(Numeric(18,5), comment="????????????????????????")
        mcks_cus_temp = Column(Numeric(18,5), comment="????????????????????????")
        msld_op_temp = Column(Numeric(18,5), comment="????????????????????????")
        msld_cus_temp = Column(Numeric(18,5), comment="????????????????????????")
        stnk_lub_temp = Column(Numeric(18,5), comment="????????????????????????")
        mcks_op_flow = Column(Numeric(18,5), comment="????????????????????????")
        mcks_cus_flow = Column(Numeric(18,5), comment="????????????????????????")
        msld_op_flow = Column(Numeric(18,5), comment="????????????????????????")
        msld_cus_flow = Column(Numeric(18,5), comment="????????????????????????")
        cus_lup_out_oprs = Column(Numeric(18,5), comment="??????????????????????????????")
        msld_lup_out_oprs = Column(Numeric(18,5), comment="??????????????????????????????")
        cus_end_oprs = Column(Numeric(18,5), comment="???????????????????????????")
        msld_end_oprs = Column(Numeric(18,5), comment="???????????????????????????")
        tri_comb_aprs = Column(Numeric(18,5), comment="??????????????????")
        pko_sfpin_aprs = Column(Numeric(18,5), comment="?????????????????????")
        clp_spr_aprs = Column(Numeric(18,5), comment="????????????????????????")
        dko_sfpin_aprs = Column(Numeric(18,5), comment="?????????????????????")
        trim_aprs = Column(Numeric(18,5), comment="????????????")
        fedw_aprs = Column(Numeric(18,5), comment="???????????????")
        clg_aprs = Column(Numeric(18,5), comment="????????????????????????")
        postub_aprs = Column(Numeric(18,5), comment="???????????????")
        trx_jaw_aprs = Column(Numeric(18,5), comment="??????????????????")
        clp_aprs = Column(Numeric(18,5), comment="????????????")
        mcg_revsp = Column(Numeric(18,5), comment="????????????")
        aut_level = Column(Numeric(18,5), comment="???????????????")
        clut_brk_cnt = Column(Integer, comment="?????????????????????")
        cnt1 = Column(Integer, comment="?????????1?????????")
        cnt2 = Column(Integer, comment="?????????2?????????")
        #27?????????
        lub_press_slder = Column(Integer, comment="????????????(????????????)")
        abn_pneu_press = Column(Integer, comment="??????")
        overload = Column(Integer, comment="??????")
        abn_driver = Column(Integer, comment="???????????????")
        pko_sfbolt = Column(Integer, comment="???????????????")
        ko_sfbolt = Column(Integer, comment="???????????????")
        lub_press_cutoff = Column(Integer, comment="????????????(?????????)")
        prob_tran_float = Column(Integer, comment="??????")
        prob_sf_door = Column(Integer, comment="?????????")
        lub_overflow = Column(Integer, comment="?????????")
        abn_temp_slder = Column(Integer, comment="??????")
        abn_lub_flow = Column(Integer, comment="????????????")
        abn_brk_pla_clut = Column(Integer, comment="?????????????????????")
        hyd_press_rscrpi_lck = Column(Integer, comment="??????????????????")
        abn_forg = Column(Integer, comment="BRANKAMP??????")
        sh_feed = Column(Integer, comment="??????")
        finish_cnt = Column(Integer, comment="??????")
        mtr_end = Column(Integer, comment="??????")
        hyd_press_grpse_lck = Column(Integer, comment="???????????????")
        oper_door = Column(Integer, comment="?????????")
        die_blkwed_hyd_oil_press = Column(Integer, comment="?????????????????????")
        die_blkcyl_hyd_oil_press = Column(Integer, comment="?????????????????????")
        pun_blkcyl_hyd_oil_press = Column(Integer, comment="????????????????????????")
        oil_level_low = Column(Integer, comment="??????(?????????)")
        mat_wgt = Column(Integer, comment="????????????")
        sf_window = Column(Integer, comment="????????????")
        motor_brk = Column(Integer, comment="???????????????")
        opr = Column(Integer, comment="??????????????????")
        error_code = Column(JSON, comment="????????????")

        __table_args__ = {'comment': '?????????'}  # ????????? #https://www.cnblogs.com/shengulong/p/9989618.html

    class DeviceEmeter(Base):
        __tablename__ = device_serial_number+"_emeter"

        upload_at = Column(DateTime, primary_key=True, server_default=func.current_timestamp(6))
        current = Column(Numeric(18,5), comment="???????????????")
        cur_cubr = Column(Numeric(18,5), comment="??????????????????")
        cur_chdr = Column(Numeric(18,5), comment="?????????????????????")
        voltage = Column(Numeric(18,5), comment="???????????????")
        volt_vubr = Column(Numeric(18,5), comment="??????????????????")
        volt_chdr = Column(Numeric(18,5), comment="?????????????????????")
        frequency = Column(Numeric(18,5), comment="??????")
        TPF = Column(Numeric(18,5), comment="???????????????")
        RELEC = Column(Numeric(18,5), comment="????????????")
        RPWD_cur = Column(Numeric(18,5), comment="?????????????????? - ??????")
        RPWD_pred = Column(Numeric(18,5), comment="?????????????????? - ??????")
        RPWD_peak = Column(Numeric(18,5), comment="?????????????????? - ??????")
        power_peak = Column(Numeric(18,5), comment="????????????")

        __table_args__ = {'comment': '????????????'}  # ?????????

    class DeviceVibmotor(Base):
        __tablename__ = device_serial_number+"_vibMotor"

        upload_at = Column(DateTime, primary_key=True, server_default=func.current_timestamp(6))
        FFT = Column(Numeric(18,5), comment="FFT")
        vibspeed_x = Column(Numeric(18,5), comment="???????????????X???")
        vibspeed_y = Column(Numeric(18,5), comment="???????????????Y???")
        vibspeed_z = Column(Numeric(18,5), comment="???????????????Z???")
        # FFT_config = Column(ElementTreeXMLType, comment="FFT????????????")
        FFT_config = Column(JSON, comment="FFT????????????")

        __table_args__ = {'comment': '???????????????'}  # ?????????

    class DeviceVibbearing(Base):
        __tablename__ = device_serial_number+"_vibBearing"

        upload_at = Column(DateTime, primary_key=True, server_default=func.current_timestamp(6))
        FFT = Column(Numeric(18,5), comment="FFT")
        vibspeed_x = Column(Numeric(18,5), comment="???????????????X???")
        vibspeed_y = Column(Numeric(18,5), comment="???????????????Y???")
        vibspeed_z = Column(Numeric(18,5), comment="???????????????Z???")
        # FFT_config = Column(ElementTreeXMLType, comment="FFT????????????")
        FFT_config = Column(JSON, comment="FFT????????????")

        __table_args__ = {'comment': '??????????????????'}  # ?????????

    class DeviceServod(Base):
        __tablename__ = device_serial_number+"_servoD"

        upload_at = Column(DateTime, primary_key=True, server_default=func.current_timestamp(6))
        # err_code = Column(ElementTreeXMLType, comment="????????????")
        err_code = Column(JSON, comment="????????????")

        __table_args__ = {'comment': '???????????????'}  # ?????????
    
    class DeviceLoadCell(Base):
        __tablename__ = device_serial_number+"_loadCell"

        upload_at = Column(DateTime, primary_key=True, server_default=func.current_timestamp(6))
        wire_weight = Column(Numeric(18,5), comment="????????????")

        __table_args__ = {'comment': '?????????'}  # ?????????
    
    return DeviceMain, DeviceEmeter, DeviceVibmotor, DeviceVibbearing, DeviceServod, DeviceLoadCell
#}}}

def create_five_device_table(app,device_serial_number):
    mysql_status = False
    err_msg = "ok"
    try:
        mysql_sensor_data = [
            {
                "name":"?????????",
                "sensor_type":0,
                "noumenon_type":"site",
                "noumenon_id":1,
                "sensor_raw_table":device_serial_number+"_main",
                "sensor_attr":"",
                "note":"Created by PaaS",
                "creator":0,
                "modifier":0,
            },
            {
                "name":"????????????",
                "sensor_type":0,
                "noumenon_type":"site",
                "noumenon_id":1,
                "sensor_raw_table":device_serial_number+"_emeter",
                "sensor_attr":"",
                "note":"Created by PaaS",
                "creator":0,
                "modifier":0,
            },
            {
                "name":"???????????????",
                "sensor_type":0,
                "noumenon_type":"site",
                "noumenon_id":1,
                "sensor_raw_table":device_serial_number+"_vibMotor",
                "sensor_attr":"",
                "note":"Created by PaaS",
                "creator":0,
                "modifier":0,
            },
            {
                "name":"??????????????????",
                "sensor_type":0,
                "noumenon_type":"site",
                "noumenon_id":1,
                "sensor_raw_table":device_serial_number+"_vibBearing",
                "sensor_attr":"",
                "note":"Created by PaaS",
                "creator":0,
                "modifier":0,
            },
            {
                "name":"???????????????",
                "sensor_type":0,
                "noumenon_type":"site",
                "noumenon_id":1,
                "sensor_raw_table":device_serial_number+"_servoD",
                "sensor_attr":"",
                "note":"Created by PaaS",
                "creator":0,
                "modifier":0,
            },
            {
                "name":"?????????",
                "sensor_type":0,
                "noumenon_type":"site",
                "noumenon_id":1,
                "sensor_raw_table":device_serial_number+"_loadCell",
                "sensor_attr":"",
                "note":"Created by PaaS",
                "creator":0,
                "modifier":0,
            },
        ]
        DbSession_mysql,metadata_mysql,engine_mysql= app.getDbSessionType(system="IOT")
        if DbSession_mysql is None:
            #??????????????????????????????
            return False,engine_mysql

        sess_mysql = DbSession_mysql()

        SensorTable = Table("sensor", metadata_mysql,  autoload=True)
        sess_mysql.execute(SensorTable.insert().values(mysql_sensor_data))
        sess_mysql.commit()
        mysql_status = True

    except Exception as e:
        print "~~~~Exception mysql insert~~~~"
        print e
        err_msg = app.catch_exception(e,sys.exc_info(),"IOT")
        print err_msg

    finally:
        if 'DbSession_mysql' in locals().keys() and DbSession_mysql is not None:
            sess_mysql.close()
            DbSession_mysql.remove()
            engengine_mysqline.dispose()
    
    if not mysql_status:
        return False,err_msg

    try:
        DbSession_pg,metadata,engine_pg= app.getDbSessionType(system="IOT",dbName="site2",forRawData="postgres",echo=True)
        if DbSession_pg is None:
            print "~~~~~db connect fail before_first_request~~~~"
            print engine_pg
            return False,engine_pg

        sess_pg = DbSession_pg()
        
        clscreate = create_default_device_models(device_serial_number)
        print "~~~clscreate~~~"
        print clscreate
        for i in clscreate:
            print "~~~i~~~"
            print i
            print i.__dict__
            print i.__tablename__
            print i.__table__
            if not engine_pg.dialect.has_table(engine_pg, i.__tablename__): 
                print "$$$$$"
                i.__table__.create(engine_pg)
            # else:
            #     print "^^^^^^"
            #     i.__table__.drop(engine_pg)

        # metadata.create_all(engine)
        return True,"ok"

    except Exception as e:
        print "~~~~Exception before_first_request~~~~"
        print e
        err_msg = app.catch_exception(e,sys.exc_info(),"IOT")
        print err_msg
        return False,err_msg

    finally:
        if 'DbSession_pg' in locals().keys() and DbSession_pg is not None:
            sess_pg.close()
            DbSession_pg.remove()
            engine_pg.dispose()

#=======================================================
# Translate dictionary constraint to statement used in create table SQL
#=======================================================
# {{{ def TransConstraint(attrDic):
def TransConstraint(attrDict):
    fName = attrDict['name']
    conStr = ''
    if attrDict.has_key('lower') and attrDict.has_key('upper'):
        conStr = "{} >= {} AND {} <= {}".format(   \
        fName ,attrDict['lower'], fName, attrDict['upper'])
    elif attrDict.has_key('lower'):
        conStr = "{} >= {}".format(fName,attrDict['lower'])
    elif attrDict.has_key('upper'):
        conStr = "{} <= {}".format(fName,attrDict['upper'])
    else:
        conStr = ''

    return conStr
# }}}

# {{{ def map_class_to_table(cls, table, entity_name, **kw):
def map_class_to_table(cls, table, entity_name, **kw):
    newcls = type(entity_name, (cls, ), {})
    mapper(newcls, table, **kw)
    return newcls
# }}}

# {{{ def fieldNameNormalize(strColName):
def fieldNameNormalize(strColName):
    return strColName.strip().lower().replace(' ','_').replace('-','_')
# }}}

# {{{ def dictSchemaToNameTypeConstraintsTuple(schemaDict):
def dictSchemaToNameTypeConstraintsTuple(schemaDict):
    # schemaDict to raw data table mapping (one-to-one)
    import logging
    logging.getLogger('fappYott').info(schemaDict)
    lstColNames = []
    lstTypeObj = []
    lstConstrain = []
    if schemaDict.has_key('name'):
        # colname = schemaDict['name'].lower().replace(' ','_')
        colname = fieldNameNormalize(schemaDict['name'])
        coltype = schemaDict['type']
        lstColNames.append(colname)
        lstTypeObj.append( TYPE_TO_SQLTYPE_DICT[coltype] )

        if schemaDict.has_key('lower_bound') \
            and schemaDict.has_key('upper_bound'):
            str1 = "{} >= {} AND {} <= {}".format(   \
                colname ,schemaDict['lower_bound'], colname, schemaDict['upper_bound'])
        elif schemaDict.has_key('lower_bound'):
            str1 = "{} >= {}".format(colname,schemaDict['lower_bound'])
        elif schemaDict.has_key('upper_bound'):
            str1 = "{} <= {}".format(colname,schemaDict['upper_bound'])
        else:
            str1 = ''

        lstConstrain.append(str1)
    maxColumnsInRawtable = 500 # have seen ~ 309 sensors in an single machine profile
    for iCol in xrange(1,maxColumnsInRawtable):
        if schemaDict.has_key('name%d'%(iCol)):
            # colname = schemaDict['name%d'%(iCol)].lower().replace(' ','_')
            colname = fieldNameNormalize(schemaDict['name%d'%(iCol)])
            coltype = schemaDict['type%d'%(iCol)]
            lstColNames.append(colname)
            lstTypeObj.append( TYPE_TO_SQLTYPE_DICT[coltype] )

            if schemaDict.has_key('lower_bound%d'%(iCol)) \
                and schemaDict.has_key('upper_bound%d'%(iCol)):
                str2 = "{} >= {} AND {} <= {}".format(   \
                    colname ,schemaDict['lower_bound%d'%(iCol)], colname, schemaDict['upper_bound%d'%(iCol)])

            elif schemaDict.has_key('lower_bound%d'%(iCol)):
                str2 = "{} >= {}".format(colname,schemaDict['lower_bound%d'%(iCol)])
            elif schemaDict.has_key('upper_bound%d'%(iCol)):
                str2 = "{} <= {}".format(colname,schemaDict['upper_bound%d'%(iCol)])
            else:

                str2 = ''
            lstConstrain.append(str2)

    return lstColNames, lstTypeObj,lstConstrain
# }}}