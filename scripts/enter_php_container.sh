#!/bin/bash

source "$(dirname "$0")/common.sh"

docker compose exec php bash -ti
