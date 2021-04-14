from celery import Celery

from celeryConfig import BaseConfig as celeryConfig

app = Celery("sapidoPaaS",
    broker="redis://root:sapido@172.16.2.55:6379/15",
    include=["celeryApp.celeryTasks"])
app.config_from_object(celeryConfig)