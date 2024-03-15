#!/usr/bin/env bash
errors=()
cd /var/www/app/src
./vendor/bin/codecept run web --fail-fast --steps -d

if [ $? -ne 0 ]; then
    error+=('web')
fi

if [ ${#error[@]} -eq 0 ]; then
    echo "web test success"
else
    echo "errors during tests:"
    echo "${error[@]}"
    exit 1
fi
