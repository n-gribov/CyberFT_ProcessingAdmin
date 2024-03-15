#!/bin/bash
SCRIPT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CONFIG_PATH="$SCRIPT_PATH/../"
export $(cat "$SCRIPT_PATH/../../.env" | xargs)

for i in "$@"
do
case $i in
    -t=*|--test=*)
    TEST="${i#*=}"
    shift # past argument=value
    ;;
    *)
            # unknown option
    ;;
esac
done

if [ "$(docker ps -a --format "{{.Names}}" | grep -c "$DOCKER_CONTAINER_NAME")" -eq 1 ]; then
    echo "Внимание"
    echo "$DOCKER_CONTAINER_NAME уже существует"
    echo "Используйте команду make start"
    exit 0
fi

if [ "$(docker images --format "{{.Repository}}:{{.Tag}}" | grep -c "$DOCKER_IMAGE_NAME")" -eq 0 ]; then
    if [ ! -f "$SCRIPT_PATH/$DOCKER_IMAGE_NAME.tar.gz" ]; then
	echo 'Downloading image file...'
    	curl -o "$SCRIPT_PATH/$DOCKER_IMAGE_NAME.tar.gz" "$DOCKER_IMAGE_URL"
    fi
    echo 'Loading image...'
    docker load < "$SCRIPT_PATH/$DOCKER_IMAGE_NAME.tar.gz"
        if [ -f "$SCRIPT_PATH/$DOCKER_IMAGE_NAME.tar.gz" ]; then
            rm -rf "$SCRIPT_PATH/$DOCKER_IMAGE_NAME.tar.gz"
        fi
fi

echo 'Creating container...'
if [ ! -z "$TEST" ];then
    DOCKER_PORT_HTTP=60
    DOCKER_PORT_HTTPS=643
fi

docker run -itd \
    -p $DOCKER_PORT_HTTP:80 \
    -p $DOCKER_PORT_HTTPS:443 \
    -v "$CONFIG_PATH/config/nginx/default.conf":/etc/nginx/conf.d/default.conf:ro \
    -v "$CONFIG_PATH/config/openssl":/home/openssl:ro \
    -v "$CONFIG_PATH/../app":/var/www/app \
    -v "$CONFIG_PATH/config/oracle/tnsnames.ora":/etc/tnsnames.ora \
    -v /etc/pgsysconfdir/pg_service.conf:/etc/pgsysconfdir/pg_service.conf \
    -v /etc/localtime:/etc/localtime:ro \
    -v /etc/timezone:/etc/timezone:ro \
    -v /etc/resolv.conf:/etc/resolv.conf:ro \
    -v "$CONFIG_PATH/config/php/fpm/php.ini":/etc/php/8.0/fpm/php.ini:ro \
    --hostname="$DOCKER_CONTAINER_HOSTNAME" \
    --name="$DOCKER_CONTAINER_NAME" \
    $DOCKER_IMAGE_NAME

if [ $? -ne 0 ]; then
    echo "Контейнер не был установлен"
    docker rm -f "$DOCKER_CONTAINER_NAME" > /dev/null 2>&1
    echo "Причина ошибки указана выше"
    exit 0
fi

if [ -z "$TEST" ];then
    docker exec -i "$DOCKER_CONTAINER_NAME" /var/www/app/utils/install.sh --test='1'
else
docker exec -i "$DOCKER_CONTAINER_NAME" /var/www/app/utils/install.sh
fi

echo 'Copy systemd script'
sudo cp "$CONFIG_PATH/scripts/cyberft-processing-admin.service" /etc/systemd/system/
sudo systemctl enable cyberft-processing-admin.service

echo 'Installing PHP app dependencies...'
docker exec --env http_proxy --env https_proxy "$DOCKER_CONTAINER_NAME" sh -c "cd ./src && composer install"
