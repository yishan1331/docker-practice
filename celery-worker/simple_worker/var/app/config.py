import ConfigParser

CONFPATH = "/var/app/config.conf"

CONFIG = ConfigParser.ConfigParser()
CONFIG.read(CONFPATH)

config = {
    #Master celery queue
    'celery_broker_ip':CONFIG.get('CeleryBroker', 'ip'),
    'celery_broker_port':CONFIG.get('CeleryBroker', 'port'),
    'celery_broker_password':CONFIG.get('CeleryBroker', 'password'),
    'celery_broker_db':CONFIG.get('CeleryBroker', 'db'),
    #Self set celery job reslut(apirecords)
    "DBREDISIp":CONFIG.get('DataBaseRedis', 'ip'),
    "DBREDISPort":CONFIG.get('DataBaseRedis', 'port'),
    "DBREDISPassword":CONFIG.get('DataBaseRedis', 'password'),
    'DBREDISDb':CONFIG.get('DataBaseRedis', 'db'),
    #writeback apirecords to Master
    "DBPOSTGRESIp":CONFIG.get('DataBasePostgresql', 'ip'),
    "DBPOSTGRESPort":CONFIG.get('DataBasePostgresql', 'port'),
    "DBPOSTGRESUser":CONFIG.get('DataBasePostgresql', 'user'),
    "DBPOSTGRESPassword":CONFIG.get('DataBasePostgresql', 'password')
}
print "##########config#########"
print config

__all__ = ['config']