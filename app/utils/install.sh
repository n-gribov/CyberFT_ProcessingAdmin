#!/bin/bash

SCRIPT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

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


echo "Установка прав"
chown -R www-data:www-data /var/www/app
chmod -R g=u /var/www/app
chmod -R 0777 /var/run/php/
echo "Запуск процессов"
"$SCRIPT_PATH/service.sh" start

sleep 0.5
if [ -f /var/www/app/src/.env ]; then
    echo "env file already exist"
    exit 1
else
    if [ -z "$TEST" ]; then
        php /var/www/app/utils/test_coverage/test_init
    else
    php /var/www/app/src/init
    fi
fi