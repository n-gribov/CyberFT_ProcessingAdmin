#!/bin/bash
SCRIPT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
export $(cat "$SCRIPT_PATH/../../.env" | xargs)


if [ "$(docker ps --format "{{.Names}}" | grep -c "$DOCKER_CONTAINER_NAME")" -eq 0 ]; then
    echo "Внимание!"
    echo "Контейнер $DOCKER_CONTAINER_NAME не установлен"
    echo "Выполните установку"
    echo "make install"
    exit 0
else
    echo "Контейнер:"
    docker ps  | grep "$DOCKER_CONTAINER_NAME"
    echo ""
    echo "Сервисы:"
    docker exec -i "$DOCKER_CONTAINER_NAME" /var/www/app/utils/service.sh status
fi