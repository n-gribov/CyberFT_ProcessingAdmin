#!/bin/bash

SCRIPT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

status() {
    service nginx status
    service php8.0-fpm status
    service redis-server status
}

start() {
    service nginx start
    service php8.0-fpm start
    service redis-server start
}

stop() {
    service nginx stop
    service php8.0-fpm stop
    service redis-server stop
}

case "$1" in
  status)
    status
    ;;
  start)
    start
    ;;
  stop)
    stop
    ;;
  restart)
    stop
    start
    ;;
  *)
    echo "Usage: service.sh {start|stop|restart}"
esac
