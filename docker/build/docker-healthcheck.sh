#!/bin/bash

# The command sends null input to handle 'not tty' problem
# Exit status of the command used in healthcheck is crucial.
# If it will finish with 0 status code, then docker container will be in healthy state,
# otherwise unhealthy.
# Timeout command exits with 124 status code if command runs more then 10s and kills php process

EXIT_CODE=$(timeout 10 php bin/console doctrine:query:sql "SELECT 1;" < /dev/null; echo $?)

if [[ ${EXIT_CODE} == "124" ]]; then
    echo "Timeout when trying to connect to the database"
    exit 1
elif [[ ${EXIT_CODE} == "0" ]]; then
    echo "Connected successfully to the database"
    exit 0
else
    echo "An error occurred. Exit status: ${EXIT_CODE}"
    exit 1
fi
