# How to use
## build images
docker build --rm -t celery-worker .
## run
docker run -dit --name helper -p 7788:6379 celery-worker