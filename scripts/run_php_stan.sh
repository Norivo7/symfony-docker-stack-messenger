#!/bin/bash

source "$(dirname "$0")/common.sh"

docker compose exec php ./vendor/bin/phpstan analyse src || exit 1
