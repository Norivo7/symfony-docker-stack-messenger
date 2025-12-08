#!/bin/bash

source "$(dirname "$0")/common.sh"

docker compose exec php bin/console messenger:consume async -vv || exit 1
