#!/bin/bash

source "$(dirname "$0")/common.sh"

docker compose exec php ./vendor/bin/php-cs-fixer fix || exit 1
