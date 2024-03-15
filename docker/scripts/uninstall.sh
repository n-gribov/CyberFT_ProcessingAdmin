#!/bin/bash

SCRIPT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
export $(cat "$SCRIPT_PATH/../../.env" | xargs)

if [ "$(docker ps -a | grep -c "$DOCKER_CONTAINER_NAME")" -eq 0 ]; then
    echo "Контейнер $DOCKER_CONTAINER_NAME отсутствует в системе"
    echo "Деинсталяция не требуется"
    exit 0
else
    docker exec -i "$DOCKER_CONTAINER_NAME" /var/www/app/utils/service.sh stop
    docker stop "$DOCKER_CONTAINER_NAME"
    docker rm -f "$DOCKER_CONTAINER_NAME"
    echo "Контейнер $DOCKER_CONTAINER_NAME был удален"
fi
