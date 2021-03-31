import time
from celery import Celery
from celery.utils.log import get_task_logger

logger = get_task_logger(__name__)

app = Celery("sapidoPaaS",broker="redis://root:sapido@172.16.2.55:6379/15")

# @app.task()
# def longtime_add(x, y):
#     logger.info('Got Request - Starting work ')
#     time.sleep(4)
#     logger.info('Work Finished ')
#     return x + y
