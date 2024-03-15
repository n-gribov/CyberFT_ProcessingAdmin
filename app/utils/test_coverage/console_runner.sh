#!/usr/bin/env bash
errors=()
cd /var/www/app/src
./yii
if [ $? -ne 0 ]; then
        error+=('console')
fi

if [ ${#error[@]} -eq 0 ]; then
    echo "console test success"
else
    echo "errors during tests:"
        echo "${error[@]}"
        exit 1
fi
