#!/usr/bin/env bash

set -e

NETWORK="taskforce-yii2"
MYSQL_CONTAINER="taskforce-yii2-mysql"
PHP_CONTAINER="taskforce-yii2-php"
PHP_IMAGE="taskforce-yii2-php"

# Create Docker network if it doesn't exist
docker network inspect "$NETWORK" >/dev/null 2>&1 \
    || docker network create "$NETWORK"

# Start MySQL container or create it if it doesn't exist
if docker container inspect "$MYSQL_CONTAINER" >/dev/null 2>&1; then
    docker start "$MYSQL_CONTAINER"

    if ! docker network inspect "$NETWORK" \
        --format '{{json .Containers}}' | grep -q "$MYSQL_CONTAINER"; then
        docker network connect "$NETWORK" "$MYSQL_CONTAINER"
    fi
else
    docker run -d \
        --name "$MYSQL_CONTAINER" \
        --network "$NETWORK" \
        -p 3309:3306 \
        --env-file .env.mysql \
        -v "$(pwd)/docker/mysql/data:/var/lib/mysql" \
        -v "$(pwd)/data/sql/schema.sql:/docker-entrypoint-initdb.d/01-schema.sql:ro" \
        mysql:8.4
fi

# Build PHP image
docker build \
    -t "$PHP_IMAGE" \
    -f docker/php/Dockerfile \
    .

# Remove old PHP container if it exists
docker rm -f "$PHP_CONTAINER" >/dev/null 2>&1 || true

# Start PHP container
docker run -d \
    --name "$PHP_CONTAINER" \
    --network "$NETWORK" \
    -p 8080:8080 \
    -v "$(pwd):/app" \
    --env-file .env \
    "$PHP_IMAGE"
