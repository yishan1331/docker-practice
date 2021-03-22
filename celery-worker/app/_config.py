# -*- coding: utf-8 -*-
"""
==============================================================================
created    : 02/08/2021

Last update: 02/08/2021

Developer: Yishan Tsai

Lite Version 1 @Yishan08032019

Filename: celeryconfig.py

Description: about celery configuration
==============================================================================
"""
from kombu import Queue
class BaseConfig(object):
    CELERY_ACCEPT_CONTENT= ['json']
    CELERY_TASK_SERIALIZER= 'json'
    CELERY_RESULT_SERIALIZER= 'json'
    CELERY_ENABLE_UTC=True
    CELERY_TIMEZONE='Asia/Taipei'

    # CELERY_ACKS_LATE=True, #https://kknews.cc/zh-tw/code/5v5vj52.html
    CELERYD_PREFETCH_MULTIPLIER=1
    CELERYD_MAX_TASKS_PER_CHILD=50 #memory leak
    CELERY_IGNORE_RESULT=True
    CELERY_STORE_ERRORS_EVEN_IF_IGNORED=True
    CELERY_TASK_CREATE_MISSING_QUEUES=False
    CELERY_QUEUES = {
        # Queue("default", routing_key = "default"),
        # Queue("queue1", routing_key = "high", queue_arguments={'maxPriority': 10}), #https://github.com/squaremo/amqp.node/issues/165
        Queue("H-queue1", routing_key = "high"),
        Queue("L-queue1", routing_key = "low")
    }
    # CELERY_TASK_ROUTES = {
    #     'celeryApp.celeryTasks.celery_trigger_specific_program': {'queue': 'H-queue1','routing_key':'high'},
    #     'celeryApp.celeryTasks.celery_post_api_count_record': {'queue': 'L-queue1','routing_key':'low'},
    #     'celeryApp.celeryTasks.celery_send_email': {'queue': 'L-queue1','routing_key':'low'},
    # }

# accept_content= ['json']
# task_serializer= 'json'
# result_serializer= 'json'
# enable_utc=True
# timezone='Asia/Taipei'
# # task_acks_late=True, #https://kknews.cc/zh-tw/code/5v5vj52.html
# worker_prefetch_multiplier=1
# task_ignore_result=True
# task_create_missing_queues=False
# task_queues = {
#     # Queue("default", routing_key = "default"),
#     # Queue("queue1", routing_key = "high", queue_arguments={'maxPriority': 10}), #https://github.com/squaremo/amqp.node/issues/165
#     Queue("H-queue1", routing_key = "high"),
#     Queue("L-queue1", routing_key = "low")
# }
# task_routes = {
#     'celeryApp.celeryTasks.celery_trigger_specific_program': {'queue': 'H-queue1','routing_key':'high'},
#     'celeryApp.celeryTasks.celery_post_api_count_record': {'queue': 'L-queue1','routing_key':'low'},
#     'celeryApp.celeryTasks.celery_send_email': {'queue': 'L-queue1','routing_key':'low'},
# }