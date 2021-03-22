from celery import Celery

from ._config import BaseConfig as celeryConfig
# from apiPortal import app

def celery_worker():
    celery = Celery("sapidoPaaS",broker="redis://root:sapido@192.168.88.71:6379/14")
    celery.config_from_object(celeryConfig)
    print("======celery_worker========")
    print(celery)
    return celery

celery = celery_worker()
celery.start()