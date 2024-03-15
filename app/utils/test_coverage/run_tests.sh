#!/usr/bin/env bash
SCRIPT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
OUTPUT_TEST_PATH="$SCRIPT_PATH/../../src/tests/_output/"
OUTPUT_CI_PATH="/home/gitlab-runner/builds/artifacts/"
source "$SCRIPT_PATH/../../../.env"
tests=(console_runner.sh web_runner.sh)

for ((g=0; g<${#tests[@]}; g++))
    do
        docker exec -i "$DOCKER_CONTAINER_NAME" /var/www/app/utils/test_coverage/"${tests[g]}"
            if [ $? -ne 0 ]; then
                error+=(${tests})
                break
            fi
    done

if [ ${#error[@]} -eq 0 ]; then
    echo "tests success"
else
        if [ "$(ls -A $OUTPUT_CI_PATH)" ]; then
            rm -rf $OUTPUT_CI_PATH/*
        fi
    cp -R "$OUTPUT_TEST_PATH" "$OUTPUT_CI_PATH"
    echo "errors during tests:"
    echo "${error[@]}"
    exit 1
fi